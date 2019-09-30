<?php
/**
 * EAP-DebitConnect (shopware Edition)
 *
 * V.O.P GmbH & Co. KG
 * Hauptstraße 62
 * 56745 Bell
 * Telefon: +49 (2652) 529-0
 * Telefax: +49 (2652) 529-129
 * E-Mail: info@eaponline.de
 * USt-IdNr.: DE 261 538 563
 * Pers. Haft. Gesellschafter:
 * V.O.P Verwaltungs GmbH, HRB 21231, Koblenz
 * Geschäftsführer: Thomas Pütz
 * Handelsregister HRA20499, Koblenz
 */

use Shopware\Components\CSRFWhitelistAware;

/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Shopware_Controllers_Backend_VOPDebitConnect extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    public function indexAction()
    {

        $csrfToken = $this->get('BackendSession')->offsetGet('X-CSRF-Token');
        $this->View()->assign(['csrfToken' => $csrfToken]);
        $smarty = $this->View();
        try {
            // TRY AUTOLOGIN

            $auth = $this->get('Auth');
            $identity = $auth->getIdentity();

            if ($identity->id) {
                $this->View()->assign('_session', $identity->sessionID);
                $this->View()->assign('_user', $identity->id);

                require_once __DIR__ . '/../../Components/DebitConnect/inc/DebitConnectCore.php';
                $cfg = new \DebitConnectCore($this->View());

                if (is_object(unserialize($this->get('backendsession')->{$cfg->sessName}))) {
                    $cfg = unserialize($this->get('backendsession')->{$cfg->sessName});
                }

                $cfg->request = $this->Request();
                $cfg->init($smarty);

                if (!$cfg->checkInstallation()) {
                    $this->View()->assign(['nomenu' => true]);
                    DC()->setSession();

                    return;
                }
                $cfg->loginData['logged_in'] = true;
                $cfg->user = $identity->id;

                $usr = [];

                if($cfg->hasvalue('webForm')){
                    $webForm = $cfg->get('webForm');

                    $current_webForms = finAPI::getCurrentWebForms();
                    $selected_webForm = $current_webForms[$webForm];
                    unset($current_webForms[$webForm]);
                    $current_webForms = array_values($current_webForms);
                    $cfg->setConf("webFormAction",json_encode($current_webForms));
                    header("Location: ".$selected_webForm->webForm);
                    exit;
                }

                if (($cfg->hasvalue('ajaxwritepayments'))) {
                    $usr['logged_in'] = $cfg->checkLogin();
                    header('Content-Type: application/json');
                    if ($usr['logged_in']) {
                        $status['state'] = DC()->hbci->writeBackUmsatz(true);
                    } else {
                        $status['state'] = 'sessionerror';
                    }
                    echo json_encode($status);

                    DC()->setSession();
                    exit();
                } elseif (($cfg->hasvalue('export'))) {
                    $usr['logged_in'] = $cfg->checkLogin();
                    $export = DC()->Export;

                    header('Content-type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="DebitConnect-Export' . $export->headLine . '.csv"');
                    $exportData = '';
                    foreach ($export->csv as $row) {
                        $exportData .= implode(';', $row) . "\r\n";
                    }

                    echo $exportData;
                    exit();
                } elseif (($cfg->hasvalue('downloadDTA'))) {
                    $usr['logged_in'] = $cfg->checkLogin();

                    $rs = DC()->db->singleResult('SELECT * from dc_dtacreatelog where id = ' . (int) $cfg->get('downloadDTA'));
                    $update = new stdClass();
                    $update->dDownload = date('Y-m-d');
                    DC()->db->dbUpdate('dc_dtacreatelog', $update, 'id = ' . (int) $cfg->get('downloadDTA'));
                    header('Content-type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="dta-' . $rs['cTransaktion'] . '.xml"');

                    echo DebitConnectCore::decrypt($rs['dtaFile']);
                    DC()->setSession();
                    exit();
                } elseif (($cfg->hasvalue('ajaxmatching'))) {
                    $usr['logged_in'] = $cfg->checkLogin();
                    header('Content-Type: application/json');
                    if ($usr['logged_in']) {
                        if (DC()->hbci->getMatching($cfg->get('ajaxmatching'))) {
                            $status['done'] = true;
                        } else {
                            $status['done'] = false;
                        }
                    } else {
                        $status['state'] = 'sessionerror';
                    }

                    echo json_encode($status);
                    DC()->setSession();
                    exit();
                } elseif (($cfg->hasvalue('syncList'))) {
                    $usr['logged_in'] = $cfg->checkLogin();
                    header('Content-Type: application/json');
                    DC()->getSyncList();
                    $outputData['order'] = DC()->syncList[0]['cRechnungsNr'];
                    echo json_encode($outputData);
                    DC()->setSession();
                    exit();
                } elseif (($cfg->hasvalue('ajaxsync'))) {
                    $usr['logged_in'] = $cfg->checkLogin();
                    header('Content-Type: application/json');
                    $outputData['order'] = DC()->syncList[0]['cAuftragsNr'];
                    $outputData['invoice'] = strlen(DC()->syncList[0]['cRechnungsNr']) > 0 ? DC()->syncList[0]['cRechnungsNr'] : '';
                    $syncErg = DC()->doSync();
                    $outputData['res'] = $syncErg['syncText'];
                    echo json_encode($outputData);
                    DC()->setSession();
                    exit();
                }

                $usr['logged_in'] = $cfg->checkLogin();

                if ($usr['logged_in'] == true && $cfg->user > 0) {
                    $smarty->assign('shopList', $cfg->shopList);

                    try {
                        if (@$cfg->hasvalue('fancy')) {
                            $this->View()->assign(['nomenu' => true]);
                            $smarty->assign('DebitConnectOutput', $cfg->fetchFancy($cfg->get('switchTo'), $smarty));
                        } else {
                            $smarty->assign('DebitConnectOutput', $cfg->fetchTemplate($cfg->current_page, $smarty));
                        }
                    }catch(Exception $e){
                        $cfg->setAlert('danger',$e->getMessage());
                    }
                    $smarty->assign('alerts',$cfg->alerts);
                } else {
                    $smarty->assign('version', DebitConnectCore::$DC_VERSION);
                    try {
                        $soap = new SoapClient(DebitConnectCore::$SOAP, ['encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1]);
                        $handshake = $soap->handshake();
                        $handshake = $handshake->status;
                    } catch (Exception $e) {
                    }
                    $smarty->assign('handshake', $handshake);
                    $this->View()->assign(['nomenu' => true]);
                    $smarty->assign('DebitConnectOutput', $smarty->fetch(__DIR__ . '/../../Components/DebitConnect/tpl/login.tpl'));
                }

                echo $cfg->smarty->fetch(__DIR__ . '/../../Components/DebitConnect/tpl/error.tpl');

                DC()->setSession();
                $this->View()->addTemplateDir(__DIR__ . '/../../Views/v_o_p_debit_connect/');
            }
        } catch (Exception $e) {
        }
    }

    public function createSubWindowAction()
    {
    }

    public function getWhitelistedCSRFActions()
    {
        return ['index'];
    }
}
