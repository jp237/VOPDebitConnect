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

require __DIR__ . '/listView.php';
require __DIR__ . '/cronjob.php';
require __DIR__ . '/shopware.php';
require __DIR__ . '/db.php';
require __DIR__ . '/shopsettings.php';
require __DIR__ . '/hbciModule.php';
require __DIR__ . '/defines.php';
require __DIR__ . '/api.php';
require __DIR__ . '/DTA.php';
require __DIR__ . '/BoniGateway.php';
require __DIR__ . '/VopLogger.php';

class DebitConnectCore
{
    public static $DC_VERSION = '0.3.20';
    public static $SOAP = 'https://api.eaponline.de/debitconnect.php?wsdl';

    public $request = null;
    public $Export;
    public $smarty;
    public $current_page = 'start';
    public $shopList;
    /** @var dbConn */
    public $db;
    public $loginData;
    public $sessName = 'DebitConnect';
    public $openConnection = null;
    /** @var DC_DataTypes */
    public $dataTypes;
    /** @var shopsettings */
    public $settings;
    /** @var API_VOP */
    public $API;
    public $listView;
    /** @var HBCI_MODULE */
    public $hbci;
    public $user = 0;
    public $mailer;
    public $zalogdate;
    public $syncList;
    public $regExList;
    public $ListViewFilter;

    public $boniGateway;

    public $lastSelected = null;
    /** @var DebitConnect_Cronjob */
    public $cronJob = null;

    public static function getVersion()
    {
        return '0.0.2';
    }

    public function hasvalue($value)
    {
        if ($this->request === null) {
            return false;
        }

        return $this->request->has($value);
    }

    public function get($value)
    {
        if ($this->request === null) {
            return null;
        }

        return $this->request->getParam($value);
    }

    public function setVOPAuftragDetail($pkOrder)
    {
        $this->db->dbQuery('DELETE FROM dc_auftragdetail where pkOrder = ' . (int) $pkOrder);
        $details = $this->dataTypes->getAuftragDetail($pkOrder);
        foreach ($details as $detail) {
            $entry = new stdClass();
            $entry->pkOrder = $pkOrder;
            $entry->tstamp = $detail['datum'];
            $entry->fWert = $detail['fWert'];
            $entry->cArt = $detail['cArt'];
            $entry->cNr = $detail['cNr'];
            $entry->kDetail = $detail['kDetail'];
            $this->db->dbInsert('dc_auftragdetail', $entry);
        }
    }

    public function setVOPAuftrag($status, $pkOrder)
    {
        if ($status == 1000) {
            // TRASH ENTY
            $insert = new stdClass();
            $insert->VOPStatus = 1000;

            return DC()->db->dbUpdate('dc_auftrag', $insert, 'id = ' . (int) $pkOrder, false);
        }
        if ($status == 101) {
            $exists = DC()->db->singleResult('SELECT count(pkOrder) as checkval,IFNULL(oldVOPStatus,0) as oldVOPStatus from dc_auftrag where id = ' . (int) $pkOrder);
            if ($exists['oldVOPStatus'] == '0') {
                return DC()->db->dbQuery('DELETE FROM dc_auftrag where id = ' . (int) $pkOrder, false);
            }
            $insert = new stdClass();
            $insert->VOPStatus = $exists['oldVOPStatus'];

            return DC()->db->dbUpdate('dc_auftrag', $insert, 'pkOrder = ' . (int) $pkOrder, false);
        }
        $auftrag = $this->dataTypes->getVOPAuftrag($pkOrder);
        foreach ($auftrag as $auftragRow) {
            $exists = DC()->db->singleResult('SELECT count(pkOrder) as checkval,IFNULL(VOPStatus,0) as vopStatus from dc_auftrag where pkOrder = ' . (int) $pkOrder);
            $insert = new stdClass();
            $insert->pkOrder = $pkOrder;
            $insert->VOPStatus = $status;
            $insert->cAnrede = $auftragRow['cAnrede'];
            $insert->cFirma = $auftragRow['cFirma'];
            $insert->cVorname = $auftragRow['cVorname'];
            $insert->cNachname = $auftragRow['cNachname'];
            $insert->cStrasse = $auftragRow['cStrasse'];
            $insert->cPLZ = $auftragRow['cPLZ'];
            $insert->cOrt = $auftragRow['cOrt'];
            $insert->cRechnungsNr = $auftragRow['cRechnungsNr'];
            $insert->cAuftragsNr = $auftragRow['cAuftragsNr'];
            $insert->cTel = $auftragRow['cTel'];
            $insert->cMail = $auftragRow['cMail'];
            $insert->cLand = $auftragRow['cLand'];
            $insert->fWert = $auftragRow['fWert'];
            $insert->fZahlung = $auftragRow['fZahlung'];
            $insert->subshopID = $this->settings->selectedShop;
            if ($status < 100) {
                $insert->dtSend = date('Y-m-d H:i:s');
            }
            if (($status == 100) && $exists['VOPStatus'] > 0) {
                $insert->oldVOPStatus = $exists['VOPStatus'];
            }

            return $exists['checkval'] == 0 ? DC()->db->dbInsert('dc_auftrag', $insert, false) : DC()->db->dbUpdate('dc_auftrag', $insert, 'pkOrder = ' . $pkOrder, false);
        }

        return false;
        //test
    }

    public function Log($art, $txt, $errormsg = 0, $pkorder = 0, $exception = null)
    {
        $logentry = new stdClass();
        $logentry->tstamp = date('Y-m-d H:i:s');
        $logentry->kUser = $this->user;
        $logentry->shopID = $this->settings->selectedShop;
        $logentry->art = $art;
        $logentry->pkOrder = $pkorder;
        $logentry->logdata = $txt;
        $logentry->errormsg = $errormsg;

        $this->db->dbInsert('dc_log', $logentry, false);
        if ($this->cronJob != null) {
            $logText = $txt;
            if ($exception != null) {
                $logText .= '<br>' . $exception->getMessage();
            }

            $isError = $errormsg > 0 ? 1 : 0;
            DC()->cronJob->Log($art, $logText, null, $isError, $pkorder);
        }
    }

    public function init($smarty)
    {
        $this->db = new dbConn();
        $this->db->dbOpen();

        CoreInstance::$getCore = $this;
        /** @return  DebitConnectCore */
        function DC()
        {
            return CoreInstance::$getCore;
        }

        if ($smarty !== null) {
            $this->smarty = $smarty;
        }

        if ($this->cronJob == null) {
            $this->cronJob = new DebitConnect_Cronjob();
        }
        if ($this->dataTypes == null) {
            $this->dataTypes = new DC_DataTypes($this->db);
        }
        if ($this->API == null) {
            $this->API = new API_VOP();
            $this->API->sendVersionInformation();
        }
    }

    public function View($key, $value)
    {
        if ($this->smarty !== null) {
            $this->smarty->assign($key, $value);
        }
        // MAYBE LOG?
    }

    public function getSettings($shop = 0)
    {
        if ($this->hbci instanceof HBCI_MODULE === false) {

        }
        $this->hbci = new HBCI_MODULE();
        if ($this->settings == null) {
            $this->settings = new shopsettings($this->db);
        }

        $this->shopList = $this->dataTypes->getShoplist($this->settings->selectedShop);

        $changeShop = DC()->get('changeShop');
        $this->settings->selectedShop = $this->getConf('selectedShop', 0, true);
        if ($changeShop > 0 || $shop > 0) {
            $this->settings->selectedShop = $shop > null ? (int) $shop : (int) $changeShop;
            if ($changeShop > 0) {
                $this->setConf('selectedShop', $changeShop, true);
                $this->settings->selectedShop = $changeShop;
                $this->hbci->matches = [];
            }
            $this->settings->flushsettings();
        }

        if ($this->settings->selectedShop == 0) {
            $this->settings->selectedShop = $this->shopList[0]['id'];
        }

        foreach ($this->shopList as $item) {
            if ($item['id'] == $this->settings->selectedShop) {
                $this->smarty->assign('SELECTED_SUBSHOP', $item['name']);
            }
        }
        $this->dataTypes->getStandardValues();

        $this->shopList = $this->dataTypes->getShoplist($this->settings->selectedShop);
        $this->settings->refresh($this->db, $this->shopList);
        $this->API = new API_VOP();
        ini_set('display_errors', DC()->getConf('debugError', '0'));

        if (DC()->settings->currentSetting->shopwareapibenutzen == 0) {
            $this->View('ERROR_MSG', 'Bitte Aktivieren Sie die Statusmeldungen zum Shop');
        }

        $this->checkRegistered();
        $this->regExList = json_decode(DC()->getConf('regex', ''), true);
        $this->checkLastSync();
    }

    public function writeListView($dataType)
    {
        $this->listView = new listView();
        $this->listView->getCurrentOrder();
        $this->listView->columns = $dataType['order'];
        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);

        $this->listView->createUnsortedList($res);

        $list = $this->listView->createListViewHeader(DC_SCRIPT);
        $list .= $this->listView->createList();
    }

    public function zeAuftrag($worker = false)
    {
        if (DC()->hasvalue('sendZahlungserinnerung') && DC()->hasvalue('cbx')) {
            foreach (DC()->get('cbx') as $rows) {
                $this->sendZahlungserinnerung($rows, false);
            }
        }
    }

    public function checkRegistered()
    {
        $check = $this->db->singleResult('SELECT activated,vopUser,vopToken from dc_firma where shopID  = ' . (int) $this->settings->selectedShop);
        if ($check['activated'] == 1 && strlen($check['vopUser']) > 0 && strlen($check['vopToken']) > 5) {
            return true;
        }
        $this->View('REG_ERROR', true);

        return false;
    }

    public function maAuftrag()
    {
        if (DC()->hasvalue('sendMahnung') && DC()->hasvalue('cbx')) {
            if ($this->checkRegistered()) {
                foreach (DC()->get('cbx') as $rows) {
                    $this->sendMahnung($rows);
                }
            }
        }
    }

    public function BoniGatewayBlackListe($pkOrder, $type = 1, $insert = true)
    {
        try {
            $blSettings = $this->settings->currentSetting->blackliste;
            if ($insert) {
                if ($type >= $this->settings->currentSetting->blackliste && $blSettings > 0) {
                    $vars = $this->dataTypes->BoniGatewayBlacklist($pkOrder);
                    if (strlen($vars['company']) == 0) {
                        $soap = $this->API->mahnwesen();
                        $user = $this->settings->registration['vopUser'];
                        $token = md5($this->settings->registration['vopToken']);
                        $response = $soap->setBlackList($user, $token, $vars['firstname'], $vars['lastname'], $vars['zipcode'], $vars['city'], $vars['street'], $vars['DateOfBirth'], (int) $pkOrder);
                        if ($response == 'ok') {
                            $update = new stdClass();
                            $update->nBlacklist = 1;
                            $this->db->dbUpdate('dc_auftrag', $update, 'pkOrder = ' . (int) $pkOrder);
                            $this->Log('Blackliste', 'Blackliste eingetragen', 0, (int) $pkOrder);
                        }
                    }
                }
            } else {
                $soap = $this->API->mahnwesen();
                $user = $this->settings->registration['vopUser'];
                $token = md5($this->settings->registration['vopToken']);
                $response = $soap->deleteFromBlackList($user, $token, (int) $pkOrder);
                if ($response == 'ok') {
                    $update = new stdClass();
                    $update->nBlacklist = 0;
                    $this->db->dbUpdate('dc_auftrag', $update, 'pkOrder = ' . (int) $pkOrder);
                    $this->Log('Blackliste', 'Blackliste entfernt', 0, (int) $pkOrder);
                }
            }
        } catch (Exception $e) {
        }
    }

    public function sendMahnung($pkOrder)
    {
        if (DC()->settings->currentSetting->shopwareapibenutzen == 0) {
            return false;
        }
        $auftragpos = $this->dataTypes->getAuftragPosQuery($pkOrder);
        $soap = $this->API->mahnwesen();
        if ($auftragpos['checksum'] >= 1) {
            $document = $auftragpos['document'] ? 'True' : 'False';
            $user = $this->settings->registration['vopUser'];
            $token = md5($this->settings->registration['vopToken']);
            $res = $soap->newMahnung($user, $token, $auftragpos['csv'], $auftragpos['checksum'], $pkOrder, $document);
            if ($res->status === 'ValidOrder' || $res->status === 'Duplicate') {
                if ($this->setVOPAuftrag(55, $pkOrder)) {
                    $this->setVOPAuftragDetail($pkOrder);
                    $this->Log('Mahnung', 'Mahnung versendet', 0, (int) $pkOrder);
                    $this->BoniGatewayBlackListe($pkOrder, 1, true);
                    $this->dataTypes->changeOrder($pkOrder, null, $this->settings->currentSetting->statusMA, 0);

                    return true;
                }
            } else {
                $this->Log('Upload', 'Error : ' . $res->ErrorMsg, 10);
                $this->View('ERROR_MSG', 'Fehler bei der Datenübertragung');
            }
        } else {
            $this->Log('Upload', 'Missing Data', 10);
        }
    }

    public function getRechnungSyncXML($pkOrder)
    {
        $rechnungen = DC()->db->getSQLResults(' SELECT kLaufnr,dGebucht from dc_rechnung where pkOrder = ' . (int) $pkOrder);

        $XML = "<xml version='1.0' encoding='utf-8'>
		<count>" . count($rechnungen) . '</count>';
        if (isset($rechnungen)) {
            foreach ($rechnungen as $rg) {
                $XML .= '<rechnung>
				<kLaufnr>' . $rg['kLaufnr'] . '</kLaufnr>
				<gebucht>' . $rg['gebucht'] . '</gebucht>
				</rechnung>';
            }
        }
        $XML .= '</xml>';

        return base64_encode($XML);
    }

    public function getSyncAuftragDetail($pkOrder)
    {
        $output = new stdClass();
        $queryAuftragDetail = ' SELECT cNr,fWert,kDetail,cArt,pkOrder from dc_auftragdetail where pkOrder = ' . (int) $pkOrder;

        $lastDetails = DC()->db->getSQLResults($queryAuftragDetail);
        $currentDetails = $this->dataTypes->getAuftragDetail($pkOrder);

        $newValue = [];
        $changeValue = [];
        $deletedValue = [];

        foreach ($currentDetails as $now) {
            $found = false;
            // GET NEW OR CHANGED
            foreach ($lastDetails as $last) {
                if ($now['cNr'] == $last['cNr'] && $now['cArt'] == $last['cArt'] && $now['kDetail'] == $last['kDetail']) {
                    if ($now['fWert'] == $last['fWert']) {
                        $found = true;
                        break;
                    }

                    $found = true;
                    $changeValue[] = $now;
                    break;
                }
            }
            if (!$found) {
                $newValue[] = $now;
            }
        }

        foreach ($lastDetails as $last) {
            $found = false;
            // GET DELETED
            foreach ($currentDetails as $now) {
                if ($now['cNr'] == $last['cNr'] && $now['cArt'] == $last['cArt'] && $now['kDetail'] == $last['kDetail']) {
                    $found = true;
                }
            }
            if (!$found) {
                $deletedValue[] = $last;
            }
        }

        $output->_new = $newValue;
        $output->_change = $changeValue;
        $output->_deleted = $deletedValue;

        return $output;
    }

    public function getSyncList($cronJob = false, $cronjobLimit = 50)
    {
        $syncQuery = 'SELECT dc_auftrag.pkOrder as pkOrderAuftrag ,dc_firma.vopUser,dc_firma.vopToken,IFNULL(statustab.id,0) as statuscount ,statustab.*,dc_auftrag.* ,s_order.cleared as paymentState
		from dc_auftrag inner join dc_firma on dc_auftrag.subshopID = dc_firma.shopID and dc_firma.activated = 1 
		    LEFT JOIN s_order on s_order.id = dc_auftrag.pkOrder left outer join dc_status statustab on statustab.pkOrder = dc_auftrag.pkOrder where ( VOPStatus = 55 OR  VOPStatus = 59 OR  VOPStatus = 95 OR  VOPStatus = 99 ) ';
        if ($cronJob) {
            $syncQuery .= ' and lastSync < ' . date('Ymd');
        }
        $syncQuery .= '  order by dc_auftrag.pkOrder DESC ';
        if ($cronJob) {
            $syncQuery .= ' LIMIT ' . (int) $cronjobLimit;
        }
        $this->syncList = $this->db->getSQLResults($syncQuery);

        if (count($this->syncList) > 0) {
            $this->Log('Synchronisierung', 'Starte Synchronisierung ' . count($this->syncList) . ' Vorgänge', 0);
        }
    }

    public function checkLastSync()
    {
        $query = ' SELECT COUNT( dc_status.id ) as anzahl FROM dc_status left join dc_auftrag on dc_auftrag.pkOrder = dc_status.pkOrder WHERE (dc_auftrag.VOPStatus = 55 or  dc_auftrag.VOPStatus = 59 or dc_auftrag.VOPStatus = 95 or dc_auftrag.VOPStatus = 99) AND lastSync < ' . (int) date('Ymd');
        $rs = DC()->db->singleResult($query);
        $counter = (int) $rs['anzahl'];
        if ((int) $rs['anzahl'] > 0) {
            $this->View('ERROR_MSG', 'Heute noch zu synchronisierende Vorgänge : ' . $counter);
        }
    }

    public function doSync()
    {
        if (isset($this->syncList[0])) {
            $syncArr = $this->getAuftragSynch($this->syncList[0]);
            unset($this->syncList[0]);
            $this->syncList = array_values($this->syncList);

            return $syncArr;
        }
    }

    public function updateVOPAuftragDetail($cmd, $data, $syncObject)
    {
        $vopCMD = ['new' => 'INSERT', 'update' => 'UPDATE', 'delete' => 'DELETE', 'change' => 'UPDATE'];
        $soap = $this->API->mahnwesen();
        $dateVOP = '00.00.0000 00:00:00';
        try {
            $parsedDate = new DateTime($data['datum']);
            $dateVOP = $parsedDate->format('d.m.Y H:i:s');
        } catch (Exception $e) {
        }
        // arr = soap.addNewChange(item.user, item.authCode, art.ToString(), rgNr.ToString(), cKorrekturNr.ToString(), dErstellt.ToString(), nZahlungsziel.ToString(), nInkassoStatus.ToString(), cBezahlt.ToString(), cKundenNr.ToString(), cFirma.ToString(), cAnrede.ToString(), cTitel.ToString(), cVorname.ToString(), cName.ToString(), cStrasse.ToString(), cPLZ.ToString(), cOrt.ToString(), cLand.ToString(), cTel.ToString(), cFax.ToString(), cEMail.ToString(), cMobil.ToString(), cZHaenden.ToString(), cGeburtstag.ToString(), nDebitorennr.ToString(), cAdressZusatz.ToString(), cSperre.ToString(), kRechnung.ToString(), betrag.ToString(), action.ToString());
        $vopParams = [
         'user' => $syncObject['vopUser'],
         'pass' => md5($syncObject['vopToken']),
         'art' => $data['cArt'],
         'cRechnungsNr' => $syncObject['cRechnungsNr'] ? $syncObject['cRechnungsNr'] : $syncObject['cAuftragsNr'],
         'cKorrekturNr' => $data['cArt'] === 'Korrektur' ? $data['cNr'] : '',
         'dErstellt' => $dateVOP,
         'nZahlungsziel' => '',
         'nInkassoStatus' => '',
         'cBezahlt' => '',
         'cKundenNr' => $syncObject['pkOrderAuftrag'],
         'cFirma' => '',
         'cAnrede' => '',
         'cTitel' => '',
         'cVorname' => $syncObject['cVorname'],
         'cName' => $syncObject['cNachname'],
         'cStrasse' => $syncObject['cStrasse'],
         'cPLZ' => $syncObject['cPLZ'],
         'cOrt' => $syncObject['cOrt'],
         'cLand' => $syncObject['cLand'],
         'cTel' => '',
         'cFax' => '',
         'cEMail' => '',
         'cMobil' => '',
         'cZHaenden' => '',
         'cGeburtstag' => '',
         'nDebitorennr' => '',
         'cAdressZusatz' => '',
         'cSperre' => '',
         'kRechnung' => $syncObject['pkOrderAuftrag'],
         'Betrag' => $data['fWert'] > 0 ? '-' . $data['fWert'] : str_replace('-', '', $data['fWert']),
         'cmd' => $vopCMD[$cmd],
         ];
        $res = $soap->addNewChange($vopParams['user'],$vopParams['pass'],$vopParams['art'],$vopParams['cRechnungsNr'],$vopParams['cKorrekturNr'],$vopParams['dErstellt'],$vopParams['nZahlungsziel'],$vopParams['nInkassoStatus'],
                    $vopParams['cBezahlt'],$vopParams['cKundenNr'],$vopParams['cFirma'],$vopParams['cAnrede'],$vopParams['cTitel'],$vopParams['cVorname'],$vopParams['cName'],$vopParams['cStrasse'],$vopParams['cPLZ'],$vopParams['cOrt'],
                        $vopParams['cLand'], $vopParams['cTel'], $vopParams['cFax'], $vopParams['cEMail'], $vopParams['cMobil'], $vopParams['cZHaenden'], $vopParams['cGeburtstag'], $vopParams['nDebitorennr'], $vopParams['cAdressZusatz'], $vopParams['cSperre'], $vopParams['kRechnung'], $vopParams['Betrag'], $vopParams['cmd']);

        if ($res->status !== 'ValidEntry') {
            $logEntry = [
                    'request' => $vopParams,
                    'response' => $res,
            ];
            DC()->Log('SyncError', print_r($res, true), 10);
            if ($this->cronJob != null) {
                $this->cronJob->Log('AddChange', 'Meldung zu V.O.P fehlgeschlagen', $logEntry, 1, $syncObject['pkOrderAuftrag']);
            }

            return false;
        }
        if ($this->cronJob != null) {
            $this->cronJob->Log('AddChange', 'Meldung ' . $vopParams['cmd'] . ' ' . $vopParams['cRechnungsNr'] . ' ' . $vopParams['dErstellt'] . ' ' . $vopParams['Betrag'] . ' erfolgreich ', null, 0, $syncObject['pkOrderAuftrag']);
        }

        if ($cmd === 'delete') {
            $query = " DELETE FROM dc_auftragdetail where cNr = '" . DC()->db->dbEscape($data['cNr']) . "' and pkOrder = " . (int) $data['pkOrder'] . " AND cArt = '" . DC()->db->dbEscape($data['cArt']) . "' AND kDetail = " . (int) $data['kDetail'];

            return  DC()->db->dbQuery($query);
        } elseif ($cmd === 'update') {
            $update = new stdClass();
            $update->fWert = $data['fWert'];
            $update->tstamp = $data['datum'];

            return DC()->db->dbUpdate('dc_auftragdetail', $update, " cNr = '" . DC()->db->dbEscape($data['cNr']) . "' and pkOrder = " . (int) $data['pkOrder'] . " AND cArt = '" . DC()->db->dbEscape($data['cArt']) . "' AND kDetail = " . (int) $data['kDetail']);
        } elseif ($cmd === 'new') {
            $newEntry = new stdClass();
            foreach ($data as $key => $value) {
                if ($key === 'datum') {
                    $key = 'tstamp';
                }
                $newEntry->{$key} = $value;
            }

            return DC()->db->dbInsert('dc_auftragdetail', $newEntry);
        }
    }

    public function getAuftragSynch($syncObject)
    {
        $ret = [];
        $ret['error'] = false;
        $soap = $this->API->mahnwesen();
        $auftrag = $syncObject;

        try {
            // SYNC ZU VOP IMMER ZUERST
            $syncVOP = $this->getSyncAuftragDetail($auftrag['pkOrderAuftrag']);
            $ret['push'] = [];

            if (count($syncVOP->_new) > 0) {
                foreach ($syncVOP->_new as $newEntry) {
                    $ret['push']['new'][] = $this->updateVOPAuftragDetail('new', $newEntry, $auftrag);
                }
            }
            if (count($syncVOP->_change) > 0) {
                foreach ($syncVOP->_change as $changedEntry) {
                    $ret['push']['change'][] = $this->updateVOPAuftragDetail('change', $changedEntry, $auftrag);
                }
            }
            if (count($syncVOP->_deleted) > 0) {
                foreach ($syncVOP->_deleted as $deletedEntry) {
                    $ret['push']['delete'][] = $this->updateVOPAuftragDetail('delete', $deletedEntry, $auftrag);
                }
            }
        } catch (Exception $exception) {
            $this->Log('Synchronisierung', $exception->getMessage(), 10);
        }
        try {
            $syncText = 'Akte in Kürze verfügbar';
            $token = md5($auftrag['vopToken']);
            $syncXML = $this->getRechnungSyncXML($auftrag['pkOrderAuftrag']);
            $res = $soap->SynchRechnung($auftrag['vopUser'], $token, (int) $auftrag['pkOrderAuftrag'], $syncXML);
            $update = new stdClass();
            $update->lastSync = date('Ymd');
            $this->db->dbUpdate('dc_status', $update, 'pkOrder = ' . (int) $auftrag['pkOrderAuftrag'], false);

            if ($res->Error === 'success') {
                $syncData = simplexml_load_string(base64_decode($res->synch));
                if ($syncData->count > 0) {
                    foreach ($syncData->rechnung as $rgRow) {
                        try {
                            $attr = $rgRow->attributes();
                            $dt = new DateTime($rgRow->dDatum);

                            $rechnungEntry = new stdClass();
                            $rechnungEntry->pkOrder = $auftrag['pkOrderAuftrag'];
                            $rechnungEntry->kLaufnr = (string) $attr['kLaufnr'];
                            $rechnungEntry->nRechJahr = (string) $rgRow->nRechja;
                            $rechnungEntry->nRechNr = (string) $rgRow->nRechnr;
                            $rechnungEntry->dErstellt = $dt->format('Y-m-d');
                            $rechnungEntry->fUst = (string) $rgRow->fUst;
                            $rechnungEntry->fSumme = (string) $rgRow->fSumme;
                            $rechnungEntry->cRechtext = (string) $attr['cRechtext'];
                            $rechnungEntry->cName1 = (string) $rgRow->cName1;
                            $rechnungEntry->cName2 = (string) $rgRow->cName2;
                            $rechnungEntry->cStrasse = (string) $rgRow->cStrasse;
                            $rechnungEntry->cPLZ = (string) $rgRow->cPLZ;
                            $rechnungEntry->cOrt = (string) $rgRow->cOrt;

                            $rechnungEntry->fZEGL = (string) $rgRow->fZEGL;
                            $rechnungEntry->fZEVOP = (string) $rgRow->fZEVOP;
                            $rechnungEntry->fVorschuss = (string) $rgRow->fVorschuss;
                            $rechnungEntry->fZahlbetrag = (string) $rgRow->fValue;
                            $rechnungEntry->fAusgezahlt = (string) $rgRow->fAusgezahlt;
                            $rechnungEntry->cTransaktion = (string) $attr['cTransaktion'];
                            $rechnungEntry->cKommentar = (string) $rgRow->cKommentar;
                            $rechnungEntry->cRichtung = (string) $rgRow->cRichtung;
                            $rechnungEntry->dGesehen = '0000-00-00';
                            $rechnungEntry->dGebucht = $rechnungEntry->dGesehen;
                            DC()->db->dbInsert('dc_rechnung', $rechnungEntry);
                            if ($rgRow->blob != null) {
                                $RechDoc = new stdClass();
                                $RechDoc->kLaufNr = $rechnungEntry->kLaufnr;
                                $RechDoc->bDocument = base64_encode(base64_decode($rgRow->blob));
                                DC()->db->dbInsert('dc_rechdoc', $RechDoc);
                            }
                            foreach ($rgRow->rechpos as $rechpos) {
                                $rechposAttr = $rechpos->attributes();
                                $rechPosEntry = new stdClass();
                                $rechPosEntry->kLaufnr = $rechnungEntry->kLaufnr;
                                $rechPosEntry->nZNR = $rechposAttr['nZNR'];
                                $rechPosEntry->nArtzeile = $rechposAttr['nArtzeile'];
                                $rechPosEntry->fMingeb = (string) $rechpos->fMingeb;
                                $rechPosEntry->fMaxgeb = (string) $rechpos->fMaxgeb;
                                $rechPosEntry->cGebText = (string) $rechpos->cGebtext;
                                $rechPosEntry->fGebuehr = (string) $rechpos->fGebuehr;
                                $rechPosEntry->fGebuehr1 = (string) $rechpos->fGebuehr1;
                                DC()->db->dbInsert('dc_rechpos', $rechPosEntry);
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
                $status = $syncData->getStatus;
                if ($auftrag['statuscount'] == 0) {
                    $insert = new stdClass();
                    $insert->pkOrder = (int) $auftrag['pkOrderAuftrag'];
                    $this->db->dbInsert('dc_status', $insert);
                }
                if (isset($status->gsoffen)) {
                    $syncText = '';
                    // AKTE NOCH NICHT ANGELEGT CONTINUE LOOP
                    $update = new stdClass();
                    if ($auftrag['fOffen'] != $status->gsoffen) {
                        $update->fOffen = number_format((string) $status->gsoffen, 2, '.', '');
                    }

                    if ($auftrag['fGesamt'] != $status->gsgenerell) {
                        $update->fGesamt = number_format((string) $status->gsgenerell, 2, '.', '');
                    }

                    if ($auftrag['nMandart'] != $status->art) {
                        $update->nMandart = $status->art;
                    }

                    if ($auftrag['nTZV'] != $status->tzv) {
                        $update->nTZV = $status->tzv;
                    }

                    if ($auftrag['nStatus'] != $status->status) {
                        $update->nStatus = $status->status;
                    }

                    if ($auftrag['nTituliert'] != $status->titel) {
                        $update->nTituliert = $status->titel;
                    }

                    if ($auftrag['nErledigt'] != $status->erledigt) {
                        $update->nErledigt = $status->erledigt;
                    }

                    if ($auftrag['nAdresse'] != $status->adresse) {
                        $update->nAdresse = $status->adresse;
                    }

                    if ($auftrag['lastLea'] != $status->lastLea) {
                        $update->lastLea = $status->lastLea;
                        $update->lastLeaBack = $status->lastLea;
                    }
                    if ($auftrag['orderhash'] != md5($status->hash)) {
                        $update->orderhash = md5($status->hash);
                    }
                    $update->lastSync = date('Ymd');
                    $this->db->dbUpdate('dc_status', $update, 'pkOrder = ' . (int) $auftrag['pkOrderAuftrag'], false);

                    $newvopstatus = 0;
                    if ($status->art == 0 && $status->erledigt == 0) {
                        $newvopstatus = 55;
                    }
                    if ($status->art == 0 && $status->erledigt == 1) {
                        $newvopstatus = 59;
                    }
                    if ($status->art == 1 && $status->erledigt == 0) {
                        $newvopstatus = 95;
                    }
                    if ($status->art == 1 && $status->erledigt == 1) {
                        $newvopstatus = 99;
                    }
                    // SETZE SHOPWARE STATUS ZUM INKASSO WENN STATUS != Komplett bezahlt
                    if ($auftrag['nMandart'] == 0 && $newvopstatus == 95) {
                        if ($auftrag['paymentState'] != $this->settings->currentHBCI['statusbezahlt']) {
                            $this->dataTypes->changeOrder((int) $auftrag['pkOrderAuftrag'], null, $this->settings->currentSetting->statusIN, 0);
                            $this->BoniGatewayBlackListe((int) $auftrag['pkOrderAuftrag'], 2, true);
                            $this->Log('Inkasso', 'aus Mahnservice in Inkasso übernommen', 0, (int) $auftrag['pkOrderAuftrag']);
                        }
                    }

                    if ($status->art == 0) {
                        $syncText .= 'Mahnung';
                    }
                    if ($status->art == 1) {
                        $syncText .= 'Inkasso';
                    }
                    $syncText .= $status->erledigt == 0 ? ' in weiterer Bearbeitung' : ' Erledigt';
                } // > GSOFFEN
                if ($newvopstatus > 0) {
                    $updateauftrag = new stdClass();
                    $updateauftrag->VOPStatus = $newvopstatus;
                    $this->db->dbUpdate('dc_auftrag', $updateauftrag, 'pkOrder = ' . (int) $auftrag['pkOrderAuftrag'], false);
                }
            } else {
                $ret['soapError'] = $res;
                $this->Log('Synchronisierung', print_r($ret, true), 10);
            }

            // SYNC KUNDE -> VOP
        } catch (Exception $e) {
            $ret['exception'] = $e->getMessage();
            $ret['error'] = true;
            $this->Log('Synchronisierung', $e->getMessage(), 10);
            $this->View('API_ERROR', $e->getMessage());
            $syncText = 'Synchronisierungsfehler';
        }

        $ret['syncText'] = $syncText;
        $ret['belege'] = $belege; // @todo undefined!

        return $ret;
    }

    public function sendInkasso($pkOrder)
    {
        if (DC()->settings->currentSetting->shopwareapibenutzen == 0) {
            return false;
        }
        $auftragpos = $this->dataTypes->getAuftragPosQuery($pkOrder);

        $soap = $this->API->mahnwesen();
        if ($auftragpos['checksum'] >= 1) {
            try {
                $document = $auftragpos['document'] ? 'True' : 'False';
                $user = $this->settings->registration['vopUser'];
                $token = md5($this->settings->registration['vopToken']);
                $res = $soap->newInkasso($user, $token, $auftragpos['csv'], $auftragpos['checksum'], $pkOrder, $document);
                if ($res->status === 'ValidOrder' || $res->status === 'Duplicate') {
                    if ($this->setVOPAuftrag(95, $pkOrder)) {
                        $this->setVOPAuftragDetail($pkOrder);
                        $this->Log('Inkasso', 'Inkassoauftrag erteilt', 0, (int) $pkOrder);
                        $this->BoniGatewayBlackListe($pkOrder, 2, true);
                        $this->dataTypes->changeOrder($pkOrder, null, $this->settings->currentSetting->statusIN, 0);

                        return true;
                    }
                }
            } catch (Exception $e) {
                $this->smarty->assign('ERROR_MSG', $e->getMessage());

                return false;
            }
        }
    }

    public function sendPapierkorb()
    {
        if (DC()->hasvalue('resetPapierkorb') && DC()->hasvalue('cbx')) {
            foreach (DC()->get('cbx') as $rows) {
                if ($this->setVOPAuftrag(101, $rows)) {
                    $this->LOG('Papierkorb', count(DC()->get('cbx')) . 'Rechnungen wiederhergestellt');
                }
            }
        }
        if (DC()->hasvalue('sendTrash') && DC()->hasvalue('cbx')) {
            foreach (DC()->get('cbx') as $rows) {
                if ($this->setVOPAuftrag(1000, $rows)) {
                    $this->LOG('Papierkorb', count(DC()->get('cbx')) . 'Rechnungen endgültig verschoben');
                }
            }
        }
        if (DC()->hasvalue('sendPapierkorb') && DC()->hasvalue('cbx')) {
            foreach (DC()->get('cbx') as $rows) {
                if ($this->setVOPAuftrag(100, $rows)) {
                    $this->LOG('Papierkorb', count(DC()->get('cbx')) . 'Rechnungen verschoben');
                }
            }
        }
    }

    public function inAuftrag()
    {
        if (DC()->hasvalue('sendInkasso') && DC()->hasvalue('cbx')) {
            if ($this->checkRegistered()) {
                foreach (DC()->get('cbx') as $rows) {
                    $this->sendInkasso($rows);
                }
            }
        }
    }

    public function getOPListe()
    {
        $headline = '';
        $type = 'OPListe';
        $progressBar = false;
        $dc_auftrag = false;
        $vopStatus = 0;
        $frist = 0;
        $aktionsbtn = null;
        $menubtn = false;
        $checkbox = false;
        $menubtn = __DIR__ . '/../tpl/btn/zahlungserinnerung.tpl';
        $headline = 'OP-Liste';
        $status = $this->settings->currentStates;
        $frist = DC()->settings->currentSetting->fristZE;
        $tpl = __DIR__ . '/../tpl/vorschlagliste.tpl';
        $aktionsbtn = ['cssclass' => 'btn btn-info btn-sm fancyboxfullscreen', 'text' => 'Übersicht', 'data-fancy-href' => DC_SCRIPT . '?switchTo=overView&noncss=1&fancy=1&pkOrder='];
        $menubtn = '';

        $this->listView = new listView($aktionsbtn, $menubtn, $checkbox, $progressBar);
        $this->listView->sessName = "lV$type";
        $this->listView->getCurrentOrder();
        //$limitfilter = array($this->listView->startCount,$this->listView->maxCount);
        $this->listView->sessName = $type;
        $dataType = $this->dataTypes->getOPListe($this->listView->startCount,$this->listView->maxCount,[
                                'column' => $this->listView->order,
                                'direction' => $this->listView->orderDir, ],
                                $this->listView->filter, $this->listView->fieldModes);

        $this->listView->columns = $dataType['order'];
        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);
        //if($type == "1")

        $this->listView->createUnsortedList($res, $dataType['count']);
        $listview = ['header' => $this->listView->createListViewHeader(DC_SCRIPT, $headline), 'table' => $this->listView->createList()];
        $this->View('listview', $listview);

        return $this->smarty->fetch($tpl);
    }

    public function getListView($type)
    {
        $this->sendPapierkorb();
        $this->zeAuftrag(false);
        $this->maAuftrag(false);
        $this->inAuftrag(false);

        $headline = '';
        $progressBar = false;
        $dc_auftrag = false;
        $vopStatus = 0;
        $frist = 0;
        $aktionsbtn = null;
        $menubtn = false;
        $checkbox = false;

        switch ($type) {
            case 1:
            $checkbox = true;
            $vopStatus = '0';
            $headline = 'Zahlungserinnerung - Vorschlagsliste';
            $status = $this->settings->currentStates;
            $frist = DC()->settings->currentSetting->fristZE;
            $tpl = __DIR__ . '/../tpl/vorschlagliste.tpl';
            $aktionsbtn = ['cssclass' => 'btn btn-info btn-sm  fancyboxfullscreen', 'text' => 'Vorschau', 'data-fancy-href' => DC_SCRIPT . '?switchTo=vorschau&noncss=1&fancy=1&order='];
            $menubtn = __DIR__ . '/../tpl/btn/zahlungserinnerung.tpl';
            break;

            case 2:
            $vopStatus = '39';
            $headline = 'Zahlungserinnerung - Versendet';
            $dc_auftrag = true;
            $tpl = __DIR__ . '/../tpl/auftrag.tpl';
            break;

            case 3:
            $checkbox = true;
            $headline = 'Mahnung - Vorschlagsliste';
            $vopStatus = '39';
            $status = (DC()->settings->currentSetting->statusZE);
            $frist = DC()->settings->currentSetting->fristMA;
            $tpl = __DIR__ . '/../tpl/vorschlagliste.tpl';
            $menubtn = __DIR__ . '/../tpl/btn/mahnung.tpl';
            break;

            case 4:
            $headline = 'Mahnung - in Bearbeitung';
            $aktionsbtn = ['cssclass' => 'btn btn-info btn-sm', 'text' => 'Akteneinsicht', 'href' => DC_SCRIPT . '?switchTo=detailansicht&back=' . $this->current_page . '&id='];
            $vopStatus = '55';
            $dc_auftrag = true;
            $tpl = __DIR__ . '/../tpl/auftrag.tpl';
            break;

            case 5:
            $checkbox = true;
            $vopStatus = '59';
            $aktionsbtn = ['cssclass' => 'btn btn-info btn-sm', 'text' => 'Akteneinsicht', 'href' => DC_SCRIPT . '?switchTo=detailansicht&back=' . $this->current_page . '&id='];
            $headline = 'Mahnung - Erledigt';
            $dc_auftrag = true;
            $tpl = __DIR__ . '/../tpl/auftrag.tpl';
            $menubtn = __DIR__ . '/../tpl/btn/erledigt.tpl';
            break;

            case 6:
            $headline = 'Inkasso - Vorschlagsliste';
            $checkbox = true;
            $vopStatus = '90';
            $status = (DC()->settings->currentSetting->statusMA);
            $frist = DC()->settings->currentSetting->fristIN;
            $tpl = __DIR__ . '/../tpl/vorschlagliste.tpl';
            $menubtn = __DIR__ . '/../tpl/btn/inkasso.tpl';
            break;

            case 7:
            $headline = 'Inkasso - in Bearbeitung';
            $aktionsbtn = ['cssclass' => 'btn btn-info btn-sm', 'text' => 'Akteneinsicht', 'href' => DC_SCRIPT . '?switchTo=detailansicht&back=' . $this->current_page . '&id='];
            $vopStatus = '95';
            $dc_auftrag = true;
            $progressBar = true;
            $tpl = __DIR__ . '/../tpl/auftrag.tpl';
            break;

            case 8:
            $checkbox = true;
            $headline = 'Inkasso - Erledigt';
            $vopStatus = '99';
            $progressBar = true;
            $aktionsbtn = ['cssclass' => 'btn btn-info btn-sm', 'text' => 'Akteneinsicht', 'href' => DC_SCRIPT . '?switchTo=detailansicht&back=' . $this->current_page . '&id='];
            $dc_auftrag = true;
            $tpl = __DIR__ . '/../tpl/auftrag.tpl';
            $menubtn = __DIR__ . '/../tpl/btn/erledigt.tpl';
            break;

            case 'papierkorb':
            $checkbox = true;
            $headline = 'Papierkorb';
            $vopStatus = '100';
            $dc_auftrag = true;
            $tpl = __DIR__ . '/../tpl/auftrag.tpl';
            $menubtn = __DIR__ . '/../tpl/btn/papierkorb.tpl';
            break;
        }

        $this->listView = new listView($aktionsbtn, $menubtn, $checkbox, $progressBar);
        $this->listView->sessName = "lV$type";
        $this->listView->getCurrentOrder();
        $limitfilter = [$this->listView->startCount, $this->listView->maxCount];
        $this->listView->sessName = $type;

        if ($dc_auftrag) {
            $dataType = $this->dataTypes->getAuftragList($this->listView->filter,
                ['column' => $this->listView->order, 'direction' => $this->listView->orderDir],
                $vopStatus, $limitfilter, $this->listView->fieldModes
            );
        } else {
            $dataType = $this->dataTypes->getOPOSList(false, $this->listView->filter, $status,
                ['column' => $this->listView->order, 'direction' => $this->listView->orderDir,],
                $vopStatus, $frist, $limitfilter, null, 0, $this->listView->fieldModes
            );
        }

        $this->listView->columns = $dataType['order'];
        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);
        //if($type == "1")

        $this->listView->createUnsortedList($res, $dataType['count']);
        $listview = ['header' => $this->listView->createListViewHeader(DC_SCRIPT, $headline), 'table' => $this->listView->createList()];
        $this->View('listview', $listview);

        return $this->smarty->fetch($tpl);
    }

    public function pageUserSettings($setting = 'reg')
    {
        $eindeutig = DC()->getConf('matched', 30, true);
        $aehnlich = DC()->getConf('similar', 20, true);
        $matching_ignore_paymentstate = DC()->getConf('matching_ignore_paymentstate', 0, true);
        $matching_threads = DC()->getConf('matching_threads', 3, true);
        $this->View('setting_art', $setting);

        $payments = DC()->dataTypes->getPaymentMethods();
        $vorkasse = DC()->dataTypes->getPaymentMethods();
        $sepapayment = DC()->dataTypes->getPaymentMethods();
        $states = DC()->dataTypes->getPaymentStatus();
        $orderstates = DC()->dataTypes->getOrderStatus();
        $kundengruppe = DC()->dataTypes->getCustomerGroups();
        $blacklist = DC()->settings->hbciBlacklist;

        $CompanyData = DC()->dataTypes->getShopCompanyData();
        $this->View('CompanyData', $CompanyData);

        $registerMethod = $this->db->singleResult('SELECT count(shopID) as registeredshops from dc_firma');
        $registered = false;

        if (DC()->hasvalue('SKRSkonto')) {
            $this->setConf('conf_skr_buchungpos', json_encode(DC()->get('skr')));
            $this->setConf('conf_skonto', json_encode(DC()->get('skonto')));
            $this->setConf('conf_skr_payment', json_encode(DC()->get('skrpayment')));
            $this->setConf('conf_skonto_zeitraum', json_encode(DC()->get('zeitraum')));
        }
        $this->settings->getSKR();

        $this->View('skr', $this->settings->SKRSkonto);
        if (DC()->hasvalue('setmatching') && DC()->hasvalue('savematching')) {
            $regex = [];
            $_regex = DC()->get('regex');
            if (count($_regex['replace']) > 0) {
                for ($i = 0, $iMax = count($_regex['replace']); $i < $iMax; ++$i) {
                    $regex[] = [$_regex['replace'][$i], $_regex['with'][$i], $_regex['comment'][$i]];
                }
            }

            DC()->setConf('regex', json_encode($regex));
            $this->regExList = json_decode(DC()->getConf('regex', json_encode(DC()->get('regex'))), true);
            DC()->setConf('matched', DC()->get('eindeutig'), true);
            DC()->setConf('similar', DC()->get('aehnlich'), true);
            DC()->setConf('matching_threads', DC()->get('matching_threads'), true);
            DC()->setConf('matching_ignore_paymentstate', DC()->get('matching_ignore_paymentstate'), true);
            $eindeutig = DC()->getConf('matched', 30, true);
            $aehnlich = DC()->getConf('similar', 20, true);
            $matching_ignore_paymentstate = DC()->getConf('matching_ignore_paymentstate', '0', true);
            $matching_threads = DC()->getConf('matching_threads', 3, true);
        }
        $this->View('regex', $this->regExList);
        $this->View('eindeutig', $eindeutig);
        $this->View('aehnlich', $aehnlich);
        $this->View('matching_threads', $matching_threads);
        $this->View('matching_ignore_paymentstate', $matching_ignore_paymentstate);

        if (DC()->hasvalue('deleteBlacklist') && DC()->get('deleteId') >= 0) {
            if (count($blacklist) === 1) {
                $newBlackList = [];
            } else {
                unset($blacklist[DC()->get('deleteId')]);
                $newBlackList = array_values($blacklist);
            }
            $blacklist = $newBlackList;
            $this->setConf('hbciBlacklist', json_encode($blacklist), true);
            DC()->settings->hbciBlacklist = json_decode($this->getConf('hbciBlacklist', json_encode([]), true), true);
        }
        if (DC()->hasvalue('setblacklist')) {
            // BLACKLISTE
            $_blacklist = DC()->get('blacklist');
            if ($_blacklist['art'] != '' && $_blacklist['cString'] != '') {
                $newEntry = ['art' => $_blacklist['art'], 'cString' => $_blacklist['cString']];
                $blacklist[] = $newEntry;
                $this->setConf('hbciBlacklist', json_encode($blacklist), true);
                DC()->settings->hbciBlacklist = json_decode($this->getConf('hbciBlacklist', json_encode([]), true), true);
            }
        }

        if (DC()->hasvalue('userregistration')) {
            $handle = md5('D3B!7C0NN3CT_' . date('Ymd') . 'AUTH_TOKEN');
            $soap = $this->API->mahnwesen();
            if (DC()->get('activate')) {
                $_reg = DC()->get('reg');

                if ($this->settings->registration['vopToken'] == $_reg['key']) {
                    $user = explode(':', $this->settings->registration['vopUser']);
                    $pwd = $this->settings->registration['vopToken'];
                    try {
                        $res = $soap->activateNewCustomer($user[0], $pwd, $handle);

                        if ($res->activated === 'yes') {
                            $dbUpdate = new stdClass();
                            $dbUpdate->activated = 1;
                            $this->db->dbUpdate('dc_firma', $dbUpdate, 'shopID = ' . (int) $this->settings->selectedShop);
                            $this->View('SUCCESS_MSG', 'Registrierung abgeschlossen');
                            $this->getSettings();
                        } else {
                            $this->View('ERROR_MSG', 'Ungültiger Aktivierungscode');
                        }
                    } catch (Exception $e) {
                        $this->View('API_ERROR', $e);
                    }
                } else {
                    $this->View('ERROR_MSG', 'Ungültiger Aktivierungscode');
                }
            }
            if (DC()->hasvalue('register')) {
                if (DC()->settings->registration == null) {
                    $sub = $this->db->singleResult('SELECT vopUser,vopToken from dc_firma limit 1');

                    $regVar = [];
                    foreach (DC()->get('reg') as $key => $val) {
                        $regVar[$key] = $this->db->dbEscape($val);
                    }

                    if (!$sub) {
                        try {
                            $res = $soap->registerNewCustomer($regVar['firma'], $regVar['unternehmer'], $regVar['strasse'], $regVar['plz'], $regVar['ort'], $regVar['land'], $regVar['tel'], $regVar['fax'], $regVar['email'], $CompanyData['host'], '', '', '', '', '', '', $regVar['ustid'], '', '', '', $regVar['unternehmer'], '', '', '', '', $regVar['email'], '', '', '', '', '', '', $handle, $regVar['vorsteuer']);
                            if ($res->internID1 > 0) {
                                $submitted = true;
                                $this->View('SUCCESS_MSG', 'Vielen Dank. Sie erhalten in Kürze eine E-Mail mit Ihrem Aktivierungcode an ' . $regVar['email']);
                                $insert = new stdClass();
                                $insert->vopUser = $res->internID1 . ':' . $res->internID2;
                                $insert->vopToken = ($res->auth);
                                $insert->shopID = $this->settings->selectedShop;
                                $insert->activated = 0;
                                $insert->registerJson = json_encode($regVar);
                                $this->db->dbInsert('dc_firma', $insert, false);
                            }
                        } catch (Exception $e) {
                            $this->View('API_ERROR', $e);
                        }
                    } else {
                        $res = $soap->addNewSub($sub['vopUser'], $sub['vopToken'], $regVar['firma'], $regVar['unternehmer'], $regVar['strasse'], $regVar['plz'], $regVar['ort'], $regVar['land'], $regVar['tel'], $regVar['fax'], $regVar['email'], $CompanyData['host'], '', '', '', '', '', '', $regVar['ustid'], '', '', '', $regVar['unternehmer'], '', '', '', '', $regVar['email'], '', '', '', '', '', '', $regVar['vorsteuer']);

                        if ($res->internID1 > 0) {
                            $submitted = true;
                            $this->View('SUCCESS_MSG', 'Registrierung abgeschlossen');
                            $insert = new stdClass();
                            $insert->vopUser = $res->internID1 . ':' . $res->internID2;
                            $insert->vopToken = $sub['vopToken'];
                            $insert->shopID = $this->settings->selectedShop;
                            $insert->registerJson = json_encode($regVar);
                            $insert->activated = 1;
                            $this->db->dbInsert('dc_firma', $insert, false);
                            $this->getSettings();
                        }
                    }
                }
                $this->getSettings();
            }
        }
        if (DC()->hasvalue('updatetemplate') && DC()->hasvalue('tpl')) {
            if (DC()->get('art') === 'zetpl') {
                $this->View('tpl_saved', $this->setConf('tpl_zahlungserinnerung', base64_encode(DC()->get('tpl'))));
            }
            if (DC()->get('art') === 'zatpl') {
                $this->View('tpl_saved', $this->setConf('tpl_zahlungseingang', base64_encode(DC()->get('tpl'))));
            }
        }
        if (DC()->hasvalue('updatesettings') || DC()->hasvalue('updatehbci')) {
            if (DC()->hasvalue('updatesettings')) {
                $this->setConf('mainsettings', json_encode(DC()->get('settings')));
                $this->setConf('payment', json_encode(DC()->get('payment')));
                $this->setConf('vorkasse', json_encode(DC()->get('vorkasse')));
                $this->setConf('sepa', json_encode(DC()->get('sepa')));
                $this->setConf('states', json_encode(DC()->get('states')));
                $this->setConf('shipping', json_encode(DC()->get('shipping')));
                $this->setConf('mahnstopCustomerGroup', json_encode(DC()->get('mahnstop')));
            } else {
                $this->setConf('hbci', json_encode(DC()->get('hbci')));
                $this->setConf('hbciCustomerGroup', json_encode(DC()->get('hbci_confirmation')));
            }
            $this->getSettings();
        }

        foreach ($this->settings->shopsArray as $shops) {
            if ($shops['shopID'] == $this->settings->selectedShop) {
                $this->View('vopUser', $shops['vopUser']);
                $this->View('vopToken', $shops['vopToken']);
                $registered = true;

                if ($shops['activated'] == 0) {
                    $this->View('activated', 1);
                }
                if ($shops['activated'] == 1) {
                    $this->View('activated', 2);
                } else {
                    $this->View('registered', true);
                }
                $regvalues = json_decode($shops['registerJson'], true);

                if (is_array($regvalues)) {
                    foreach ($regvalues as $key => $value) {
                        $this->View($key, $value);
                    }
                }
            }
        }
        if (!$registered) {
            $this->View('not_registered', 'true');
        }

        foreach ($this->settings->currentSetting as $key => $value) {
            $this->View($key, $value);
        }

        $this->View('hbci', $this->settings->currentHBCI);

        if (DC()->hasvalue('updatecronjob')) {
            $this->setConf('cronjob', json_encode(DC()->get('cronjob')));
            $this->getSettings();
        }

        $kundengruppe_ze = $kundengruppe;
        for ($i = 0, $iMax = count($kundengruppe_ze); $i < $iMax; ++$i) {
            if (@in_array($kundengruppe_ze[$i]['id'], $this->settings->cronjob->ze->kundengruppe)) {
                $kundengruppe_ze[$i]['dc_config'] = 1;
            }
        }

        $this->View('kundengruppe_ze', $kundengruppe_ze);

        $kundengruppe_ma = $kundengruppe;
        for ($i = 0, $iMax = count($kundengruppe_ma); $i < $iMax; ++$i) {
            if (@in_array($kundengruppe_ma[$i]['id'], $this->settings->cronjob->ma->kundengruppe)) {
                $kundengruppe_ma[$i]['dc_config'] = 1;
            }
        }
        $this->View('kundengruppe_ma', $kundengruppe_ma);

        $this->View('cronjob', $this->settings->cronjob);

        $mahnstopCustomerGroup = $kundengruppe;
        for ($i = 0, $iMax = count($kundengruppe_ma); $i < $iMax; ++$i) {
            if (@in_array($mahnstopCustomerGroup[$i]['id'], $this->settings->mahnstopCustomerGroup)) {
                $mahnstopCustomerGroup[$i]['dc_config'] = 1;
            }
        }
        $this->View('mahnstopCustomerGroup', $mahnstopCustomerGroup);
        for ($i = 0, $iMax = count($payments); $i < $iMax; ++$i) {
            if (count($this->settings->currentPayments) > 0) {
                if (@in_array($payments[$i]['id'], $this->settings->currentPayments)) {
                    $payments[$i]['dc_config'] = 1;
                }
            }
        }
        for ($i = 0, $iMax = count($sepapayment); $i < $iMax; ++$i) {
            if (count($this->settings->currentSEPA) > 0) {
                if (@in_array($sepapayment[$i]['id'], $this->settings->currentSEPA)) {
                    $sepapayment[$i]['dc_config'] = 1;
                }
            }
        }
        for ($i = 0, $iMax = count($vorkasse); $i < $iMax; ++$i) {
            if (count($this->settings->currentVorkasse) > 0) {
                if (@in_array($vorkasse[$i]['id'], @$this->settings->currentVorkasse)) {
                    $vorkasse[$i]['dc_config'] = 1;
                }
            }
        }
        $this->View('vorkassepayments', $vorkasse);
        $this->View('payments', $payments);
        $this->View('sepapayments', $sepapayment);

        for ($i = 0, $iMax = count($states); $i < $iMax; ++$i) {
            if (count($this->settings->currentStates) > 0) {
                if (in_array($states[$i]['id'], $this->settings->currentStates)) {
                    $states[$i]['dc_config'] = 1;
                }
            }
        }

        for ($i = 0, $iMax = count($orderstates); $i < $iMax; ++$i) {
            if (count($this->settings->shipping->states) > 0) {
                if (in_array($orderstates[$i]['id'], $this->settings->shipping->states)) {
                    $orderstates[$i]['dc_config'] = 1;
                }
            }
        }

        if ($setting === 'hbci') {
            $this->View('blackliste', DC()->settings->hbciBlacklist);

            $hbcicustomergroup = $kundengruppe;
            for ($i = 0, $iMax = count($kundengruppe_ma); $i < $iMax; ++$i) {
                if (@in_array($hbcicustomergroup[$i]['id'], $this->settings->hbciMailCustomerGroupDisable)) {
                    $hbcicustomergroup[$i]['dc_config'] = 1;
                }
            }
            $this->View('hbcicustomergroup', $hbcicustomergroup);
        }

        if (DC()->get('art') === 'zatpl') {
            $this->View('tpl', base64_decode($this->getConf('tpl_zahlungseingang', base64_encode(file_get_contents(__DIR__ . '/../tpl/bestaetigung.tpl')))));
        }
        if (DC()->get('art') === 'zetpl') {
            $this->View('tpl', base64_decode($this->getConf('tpl_zahlungserinnerung', base64_encode(file_get_contents(__DIR__ . '/../tpl/standardtemplate.tpl')))));
        }
        $this->View('states', $states);
        $this->View('orderstates', $orderstates);
        $this->View('settingsShipping', $this->settings->shipping);
        if (DC()->settings->currentSetting->shopwareapibenutzen == 1) {
            $this->dataTypes->testApi();
        }

        return $this->smarty->fetch(__DIR__ . '/../tpl/usersettings.tpl');
    }

    public function HBCIWriteBackPayments()
    {
        $this->View('synccounter', DC()->hbci->getCountPaymentsReady());
        $this->View('doPayments', true);
        $this->View('matching_threads', DC()->getConf('matching_threads', 3, true));

        return $this->smarty->fetch(__DIR__ . '/../tpl/hbciprogress.tpl');
    }

    public function HBCIMatching()
    {
        DC()->hbci->UmsaetzeFromDB(false);
        DC()->dataTypes->getZahlungsabgleichBestellungen();
        $this->View('doMatching', true);
        $this->View('synccounter', count(DC()->hbci->umsaetze));
        $this->View('matching_threads', DC()->getConf('matching_threads', 3, true));
        $this->View('ordercounter', count(DC()->hbci->bestellungen));

        return $this->smarty->fetch(__DIR__ . '/../tpl/hbciprogress.tpl');
        //DC()->hbci->getMatching();
    }

    public function HBCICSVFiles()
    {
        $csvFiles = DC()->hbci->getCSVList();
        if (count($csvFiles) > 0) {
            $this->View('hbci_csv_list', $csvFiles);
        }

        return $this->smarty->fetch(__DIR__ . '/../tpl/hbcicsvdata.tpl');
    }

    public function Zahlungsabgleich()
    {
        $hbciProfiles = $this->settings->hbciProfiles;
        if ($this->hasvalue('resetMatches')) {
            $this->hbci->matches = [];
        }

        $csvFiles = DC()->hbci->getCSVList();
        if (count($csvFiles) > 0) {
            $this->View('hbci_csv_list', $csvFiles);
        }
        if (DC()->hasvalue('CSVFile') && DC()->hasvalue('GetCSV')) {
            if (strtoupper(substr(DC()->get('CSVFile'), -3)) === 'CSV') {
                DC()->hbci->abrufUmsatzCSV(DC()->get('CSVFile'), ';', '', '');
            }
        }

        $active = count($hbciProfiles) > 0;
        if ($active) {
            $this->View('konten', DC()->settings->currentHBCI['konten']);
            if (@DC()->get('requesthbci')) {
                $ProfileItem = explode(';', DC()->get('selectedKonten'));
                $from = new DateTime(DC()->get('von'));
                $to = new DateTime(DC()->get('bis'));

                DC()->hbci->abrufUmsatz($ProfileItem[0], $ProfileItem[1], $from, $to);

                $logger = DC()->hbci->logger;
                if (count($logger->msg_error) > 0) {
                    $errormsg = '';
                    foreach ($logger->msg_error as $message) {
                        $errormsg .= $message . '<br>';
                    }
                    $this->View('ERROR_MSG', $errormsg);
                }
            }
        }
        if (DC()->hasvalue('HBCIDelete')) {
            if (count(DC()->get('selected')) > 0) {
                foreach (DC()->get('selected') as $key => $item) {
                    $update = new stdClass();
                    $update->dAbgleich = date('Y-m-d H:i:s');
                    $update->nNichtverbuchen = 1;
                    DC()->db->dbUpdate('dc_umsatz', $update, 'kUmsatz = ' . (int) $item);
                }
            }
        }
        if (DC()->hasvalue('HBCIdelUmsatz')) {
            /*
            foreach(DC()->get('selected'] as $key => $item)
                {
                     $sqlq = " DELETE FROM dc_umsatz where kUmsatz = ".(int) $item;
                     DC()->db->dbQuery($sqlq);
                }
            */
        }
        DC()->hbci->UmsaetzeFromDB();
        DC()->dataTypes->getZahlungsabgleichBestellungen();

        if (DC()->hasvalue('HBCIAction') && DC()->hasvalue('HBCItoDB')) {
            //DC()->get('HBCItoDB') = null;
            $this->hbci->writeBackUmsatz();
            $this->Zahlungsabgleich();
        }

        $this->View('Umsatzcounter', count(DC()->hbci->umsaetze));
        $this->View('matches', DC()->hbci->matches);
        //	$this->View("bestellData",DC()->hbci->bestellungen);

        $this->View('umsatzData', DC()->hbci->umsaetze);
        $this->View('zaActive', $active);

        return $this->smarty->fetch(__DIR__ . '/../tpl/abgleich.tpl');
    }

    public function setIgnoreAbort()
    {
        set_time_limit(0);
        @ignore_user_abort(0);
        ini_set('max_execution_time', 0);
    }

    public function createDTA()
    {
        if (DC()->hasvalue('createDTA') && count(DC()->get('cbx')) > 0) {
            $selectedKonto = explode(';', DC()->get('konto'));
            if (count($selectedKonto) < 2) {
                $this->View('ERROR_MSG', 'Fehler bei Kontoauswahl');

                return;
            }

            foreach ($this->settings->hbciProfiles as $profile) {
                foreach ($profile->profileData->konto as $konto) {
                    if ($profile->id == $selectedKonto[0] && $konto->IBAN == $selectedKonto[1]) {
                        if (strlen($konto->OWNER) < 1) {
                            $this->View('ERROR_MSG', 'Kein Kontoinhaber zu ' . $konto->IBAN . ' (' . $profile->profileName . ') hinterlegt');

                            return;
                        }

                        $addedTransfers = [];
                        $ident_number = $this->dataTypes->getIdentNumber($this->getShopId());
                        if (strlen($ident_number) < 5) {
                            $this->View('ERROR_MSG', 'Keine Gläubiger Identifikationsnummer zu diesem Shop hinterlegt');

                            return;
                        }

                        $getNewId = 'select IFNULL(MAX(IFNULL(idTransaktion,0)),0)+1 as newTransAktionId from dc_dtacreatelog';
                        $rs = DC()->db->singleResult($getNewId);
                        $newId = $rs['newTransAktionId'];
                        if ($newId < 1) {
                            $this->View('ERROR_MSG', 'Konnte keine TransktionsId erzeugen');

                            return;
                        }

                        $transactionName = md5($ident_number . $newId);
                        $payment_name = 'Payment-Id-' . $newId . '-';

                        $dtaXML = new DTA($transactionName, 'Me', $konto, $konto->OWNER, $ident_number, $transactionName, $payment_name);

                        $amountTransfer = '0.00';
                        foreach (DC()->get('cbx') as $pkOrder) {
                            $dtaRow = $this->dataTypes->createDTA((int) $pkOrder);
                            // $profile = PROFIL , $konto = KONTO
                            if ($dtaRow['amount'] > 0 && strlen($dtaRow['iban']) > 0 && strlen($dtaRow['bic']) > 0 && strlen($dtaRow['bankname']) > 0 &&
                                                            ((strlen($dtaRow['company']) > 0) || (strlen($dtaRow['lastname']) > 0 && strlen($dtaRow['firstname']) > 0))) {
                                // Add a Single Transaction to the named payment
                                $dtaXML->xmlFile->addTransfer($payment_name, [
                                        'amount' => number_format($dtaRow['amount'], '2', '.', ''),
                                        'debtorIban' => trim(strtoupper($dtaRow['iban'])),
                                        'debtorBic' => trim(strtoupper($dtaRow['bic'])),
                                        'debtorName' => $dtaRow['company'] ? $dtaRow['company'] : $dtaRow['firstname'] . ' ' . $dtaRow['lastname'],
                                        'debtorMandate' => $dtaRow['ordernumber'],

                                        //'debtorMandateSignDate' => '13.10.2012',
                                        'remittanceInformation' => $konto->VWZ . ' ' . $dtaRow['ordernumber'] . ' ' . $dtaRow['invoicenumber'] . ' ' . $dtaRow['customernumber'],
                                    ]);

                                // Retrieve the resulting XML
                                $addedTransfers[] = ['id' => $pkOrder, 'amount' => $dtaRow['amount']];
                                $amountTransfer += $dtaRow['amount'];
                            } else {
                                print_r($dtaRow);
                                $this->View('ERROR_MSG', 'Fehler in Daten ' . print_r($dtaRow, true));
                            }
                        }
                        if (count($addedTransfers) < 1) {
                            $this->View('ERROR_MSG', 'Keine Zahlungen hinzugefügt');

                            return;
                        }

                        $dbEntrySepaFile = new stdClass();
                        $dbEntrySepaFile->idTransaktion = $newId;
                        $dbEntrySepaFile->pkOrder = 0;
                        $dbEntrySepaFile->nType = 0;
                        $dbEntrySepaFile->dateCreated = date('Y-m-d');
                        $dbEntrySepaFile->dtaFile = self::encrypt($dtaXML->xmlFile->asXML());
                        $dbEntrySepaFile->cTransaktion = $transactionName;
                        $dbEntrySepaFile->idProfile = $profile->id;
                        $dbEntrySepaFile->idKonto = $konto->IBAN;
                        $dbEntrySepaFile->shopID = $this->getShopId();
                        $dbEntrySepaFile->nAnzahl = count($addedTransfers);
                        $dbEntrySepaFile->fSumme = number_format($amountTransfer, 2, '.', '');

                        if ($this->db->dbInsert('dc_dtacreatelog', $dbEntrySepaFile)) {
                            foreach ($addedTransfers as $order) {
                                $dbEntrySingleRow = new stdClass();
                                $dbEntrySingleRow->idTransaktion = $newId;
                                $dbEntrySingleRow->pkOrder = $order['id'];
                                $dbEntrySingleRow->nType = 1;
                                $dbEntrySingleRow->dateCreated = date('Y-m-d');
                                $dbEntrySingleRow->cTransaktion = $transactionName;
                                $dbEntrySingleRow->shopID = $this->getShopId();
                                $dbEntrySingleRow->nAnzahl = 1;
                                $dbEntrySingleRow->fSumme = $order['amount'];
                                $this->db->dbInsert('dc_dtacreatelog', $dbEntrySingleRow);
                            }
                        }
                    }
                }
            }
        }
    }

    public function getDTAList()
    {
        try {
            $this->createDTA();
        } catch (Exception $e) {
            echo $e->getMessage() . '<br>' . $e->getTraceAsString();
        }
        $dtaCreatedList = $this->db->getSQLResults('select * from dc_dtacreatelog left outer join dc_umsatz on dc_umsatz.kUmsatz = dc_dtacreatelog.kUmsatz where dc_dtacreatelog.nType = 0 and shopId = ' . (int) $this->getShopId() . ' order by id desc ');
        $profiles = $this->settings->hbciProfiles;
        $active = count($profiles) > 0;
        if ($active) {
            $this->View('profiles', $profiles);
        }
        $headline = '';
        $type = 'dtaList';
        $progressBar = false;
        $dc_auftrag = false;
        $vopStatus = 0;
        $frist = 0;
        $aktionsbtn = null;
        $menubtn = false;
        $checkbox = true;
        $headline = 'Lastschriften - SEPA-XML Erstellen';
        //	$aktionsbtn = array("cssclass" => "button success fancyboxfullscreen","text" => "Übersicht","data-fancy-href" => DC_SCRIPT."?switchTo=overView&noncss=1&fancy=1&pkOrder=");
        $menubtn = __DIR__ . '/../tpl/btn/dta.tpl';

        $this->listView = new listView($aktionsbtn, $menubtn, $checkbox, $progressBar);
        $this->listView->sessName = "lV$type";
        $this->listView->getCurrentOrder();
        //$limitfilter = array($this->listView->startCount,$this->listView->maxCount);
        $this->listView->sessName = $type;
        $dataType = $this->dataTypes->getDTAList($this->listView->startCount,$this->listView->maxCount,[
                                'column' => $this->listView->order,
                                'direction' => $this->listView->orderDir, ],
                                $this->listView->filter, $this->listView->fieldModes);

        $this->listView->columns = $dataType['order'];
        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);
        //if($type == "1")

        $this->listView->createUnsortedList($res, $dataType['count']);
        $listview = ['header' => $this->listView->createListViewHeader(DC_SCRIPT, $headline), 'table' => $this->listView->createList()];
        $this->View('listview', $listview);
        $this->View('dtaList', $dtaCreatedList);

        return $this->smarty->fetch(__DIR__ . '/../tpl/dtaList.tpl');
    }

    public function FILE_LOG($message, $logfile = 'File_LOG.txt')
    {
        $logreader = fopen($logfile, 'ab+');
        fwrite($logreader, $message . "\r\n");
        fclose($logreader);
    }

    public function getBoniGatewaySettings()
    {
        $settings = [];
        $selectedShop = (int) $this->settings->selectedShop;
        $entrys = DC()->db->getSQLResults('SELECT art,datavalue from dc_gatewaymeta where nType = 0 and  shopID = ' . $selectedShop);

        foreach ($entrys as $entry) {
            $settings[$entry['art']] = is_object(json_decode($entry['datavalue'])) ? json_decode($entry['datavalue'], true) : $entry['datavalue'];
        }

        return $settings;
    }

    public function BoniGatewayLog()
    {
        if ($this->zalogdate == null) {
            $this->zalogdate = date('d.m.Y');
        }
        if (DC()->hasvalue('datefilter')) {
            $this->zalogdate = DC()->get('datefilter');
        }
        $dt = new DateTime($this->zalogdate);
        $dt = $dt->format('d.m.Y');
        //DC()->get('datefilter') = $dt;
        $aktionsbtn = false;
        $this->listView = new listView($aktionsbtn, '', false);
        $this->listView->sessName = 'bonilog';
        $this->listView->getCurrentOrder();
        $dataType['order'] = [
            'PK' => [false, 'logtab.logid', 'Key', false],
            'customer_vname' => [true, 'logtab.customer_vname', 'Vorname', true],
            'customer_nname' => [true, 'logtab.customer_nname', 'Nachname', true],
            'customer_firma' => [true, 'logtab.customer_firma', 'Firma', true],
            'tstamp' => [true, 'logtab.tstamp', 'Datum', false],
            'zahlungsart' => [true, 'logtab.zahlungsart', 'Zahlungsart', true],
            'cArt' => [true, 'logtab.cArt', 'Art', true],
            'ResponseText' => [true, 'logtab.responseText', 'Antwort', false],
            'scoreinfo' => [true, 'logtab.scoreInfo', 'ScoreInfo', false],
            'cssclass' => [false, 'cssclass', 'cssclass', false],
        ];

        $this->listView->columns = $dataType['order'];
        $dataType['query'] = "SELECT logtab.*, logid as id , CASE WHEN logtab.ergebnis = 1 THEN 'error' ELSE 'success' END as cssclass from dc_gatewaylog  as logtab where 1=1 ";
        $setFilter = false;
        if (DC()->hasvalue('setFilter')) {
            foreach (DC()->get('setFilter') as $key => $value) {
                if ($value != '') {
                    $setFilter = true;
                    $dataType['query'] .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
                }
            }
        }

        if ($setFilter == false) {
            $dataType['query'] .= ' ORDER BY ' . $this->listView->order . ' ' . $this->listView->orderDir;
        }

        $limitfilter = [$this->listView->startCount, $this->listView->maxCount];
        $this->listView->setFilter(DC()->get('setFilter'));
        $resCount = $this->db->getSQLResults($dataType['query']);

        $dataType['query'] .= ' LIMIT ' . implode(',', $limitfilter);
        $res = $this->db->getSQLResults($dataType['query']);
        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);
        $this->listView->createUnsortedList($res, count($resCount));
        $listview = ['header' => $this->listView->createListViewHeader(DC_SCRIPT, 'Bonitätsprüfung - Protokoll'), 'table' => $this->listView->createList()];
        $this->View('listview', $listview);

        return $this->smarty->fetch(__DIR__ . '/../tpl/gatewaylog.tpl');
    }

    public function getBoniGatewayLanguage()
    {
        $settingslang = [];
        $lang = [];
        $selectedShop = (int) $this->settings->selectedShop;
        $entrys = DC()->db->getSQLResults('SELECT art,datavalue,comment from dc_gatewaymeta where nType = 1 and  shopID = ' . $selectedShop);

        foreach ($entrys as $entry) {
            $settingslang[$entry['art']]['value'] = $entry['datavalue'];
        }

        $stdentrys = DC()->db->getSQLResults('SELECT art,datavalue,comment from dc_gatewaymeta where nType = 1 and  shopID = 0');
        foreach ($stdentrys as $entry) {
            $lang[$entry['art']]['value'] = isset($settingslang[$entry['art']]) ? $settingslang[$entry['art']]['value'] : $entry['datavalue'];
            $lang[$entry['art']]['shopID'] = isset($settingslang[$entry['art']]) ? $selectedShop : 0;
            $lang[$entry['art']]['comment'] = $entry['comment'];
            $lang[$entry['art']]['key'] = $entry['art'];
        }

        return $lang;
    }

    public function BoniGatewaySettings()
    {
        $selectedShop = (int) $this->settings->selectedShop;
        $gatewaySettings = $this->getBoniGatewaySettings();
        $gatewayLanguage = $this->getBoniGatewayLanguage();

        if (DC()->hasvalue('gateway')) {
            foreach (DC()->get('gateway') as $key => $value) {
                if (array_key_exists($key, $gatewaySettings)) {
                    $update = new stdClass();
                    $update->datavalue = is_array($value) ? json_encode($value) : $this->db->dbEscape($value);
                    DC()->db->dbUpdate('dc_gatewaymeta', $update, "art = '" . $this->db->dbEscape($key) . "' and nType = 0 and shopID = " . $selectedShop);
                } else {
                    $insert = new stdClass();
                    $insert->datavalue = is_array($value) ? json_encode($value) : $this->db->dbEscape($value);
                    $insert->art = $key;
                    $insert->nType = 0;
                    $insert->shopID = $selectedShop;
                    DC()->db->dbInsert('dc_gatewaymeta', $insert);
                }
            }
        }

        if (DC()->hasvalue('lang')) {
            foreach (DC()->get('lang') as $key => $value) {
                if ($gatewayLanguage[$key]['shopID'] > 0) {
                    $update = new stdClass();
                    $update->datavalue = is_array($value) ? json_encode($value) : $this->db->dbEscape($value);
                    DC()->db->dbUpdate('dc_gatewaymeta', $update, "art = '" . $this->db->dbEscape($key) . "'  and nType = 1  and shopID = " . $selectedShop);
                } else {
                    $insert = new stdClass();
                    $insert->datavalue = is_array($value) ? json_encode($value) : $this->db->dbEscape($value);
                    $insert->art = $key;
                    $insert->nType = 1;
                    $insert->shopID = $selectedShop;
                    DC()->db->dbInsert('dc_gatewaymeta', $insert);
                }
            }
        }
        $gatewayLanguage = $this->getBoniGatewayLanguage();
        $gatewaySettings = $this->getBoniGatewaySettings();
        $_customergroups = [];
        foreach (DC()->dataTypes->getCustomerGroups() as $group) {
            $boni = $group;
            $ident = $group;
            $boni['conf_value'] = $gatewaySettings['boni_customergroup'][$group['id']];
            $ident['conf_value'] = $gatewaySettings['ident_customergroup'][$group['id']];
            $_customergroups['boni'][] = $boni;
            $_customergroups['ident'][] = $ident;
        }

        $_payments = [];
        foreach (DC()->dataTypes->getPaymentMethods() as $payment) {
            $payment['conf_value'] = $gatewaySettings['boni_payments'][$payment['id']];
            $_payments[] = $payment;
        }

        $_shipping = [];
        foreach (DC()->dataTypes->getShippingMethods() as $shipping) {
            $shipping['conf_value'] = $gatewaySettings['ident_shipping'][$shipping['id']];
            $_shipping[] = $shipping;
        }
        try {
            $client = new SoapClient('https://api.eaponline.de/bonigateway.php?wsdl', ['cache_wsdl' => WSDL_CACHE_NONE]);
            $gwusr = $gatewaySettings['username'];
            $gwpwd = md5($gatewaySettings['passwd']);
            if (strlen($gwusr) > 0 && strlen($gwpwd) == 32) {
                $result = $client->getGatewayLogin($gwusr, $gwpwd);
                if ($result->status === 'True') {
                    $this->View('eap_state', 'LOGIN');
                    $projekte = $client->getProject($gwusr, $gwpwd, '1');

                    if (isset($projekte[0]->Error)) {
                        $this->View('eap_state', 'NOPROJECT');
                    } else {
                        $encProjects = [];
                        $b2bProjects = [];
                        $b2bcount = 0;
                        $b2ccount = 0;
                        foreach ($projekte as $iValue) {
                            if ($iValue->projecttype === 'B2C') {
                                $encProjects[$b2ccount] = new stdClass();
                                $encProjects[$b2ccount]->bezeichnung = ($iValue->bezeichnung);
                                $encProjects[$b2ccount]->row = $b2ccount;
                                $encProjects[$b2ccount]->projectvalue = ($iValue->projectvalue);
                                ++$b2ccount;
                            } else {
                                $b2bProjects[$b2bcount] = new stdClass();
                                $b2bProjects[$b2bcount]->bezeichnung = ($iValue->bezeichnung);
                                $b2bProjects[$b2bcount]->row = $b2bcount;
                                $b2bProjects[$b2bcount]->projectvalue = ($iValue->projectvalue);
                                ++$b2bcount;
                            }
                        }

                        $this->View('eap_projekte', $encProjects);
                        $this->View('eap_projekteb2b', $b2bProjects);
                    }
                } else {
                    $this->View('eap_state', 'NOLOGIN');
                }
            }// END IF STRLEN USER STRLEN PWD
        } catch (Exception $e) {
            $this->View('eap_state', 'COMERR');
            $this->View('eap_exception', $e);
        }

        $this->View('lang', $gatewayLanguage);
        $this->View('customergroups', $_customergroups);
        $this->View('payments', $_payments);
        $this->View('shipping', $_shipping);
        $this->View('gateway', $gatewaySettings);

        return $this->smarty->fetch(__DIR__ . '/../tpl/bonigateway.tpl');
    }

    public function getShopId()
    {
        return (int) $this->settings->selectedShop;
    }

    public function hbciProfiles()
    {
        if (DC()->hasvalue('newProfile') && DC()->get('ProfileName') != '') {
            $entry = new stdClass();
            $entry->profileName = DC()->get('ProfileName');
            $entry->shopID = $this->getShopId();
            $entry->profileData = json_encode([]);
            $this->db->dbInsert('dc_hbciprofiles', $entry);
        }

        $profiles = $this->settings->getHBCIProfiles();

        if (DC()->hasvalue('updateProfile') && DC()->hasvalue('profile')) {
            $this->settings->updateProfile((int) DC()->get('getProfileId'), DC()->get('profile'));
            $profiles = $this->settings->getHBCIProfiles();
        }
        $this->View('profiles', $profiles);
        if (DC()->hasvalue('getProfileId')) {
            // BEI GEWÄHLTEM PROFIL HBCI ABRUF FÜR KONTEN DURCHFÜHREN
            $selectedProfile = $profiles[DC()->get('getProfileId')];
            if ($selectedProfile->profileData->blz != '' && $selectedProfile->profileData->pin != '' && $selectedProfile->profileData->url != '' && $selectedProfile->profileData->alias != '') {
                try {
                    $this->View('konten', DC()->hbci->returnKonten($selectedProfile));
                } catch (Exception $e) {
                    $this->View('HBCI_FAULT', $e->getMessage());
                }
            }
            if (DC()->hbci->hbci != null) {

                $logger = DC()->hbci->logger;
                if (count($logger->msg_error) > 0) {
                    $errormsg = '';
                    foreach ($logger->msg_error as $message) {
                        $errormsg .= $message . '<br>';
                    }
                    $this->View('HBCI_FAULT', $errormsg);
                }
            }

            $this->View('selected', $selectedProfile);
        }

        return $this->smarty->fetch(__DIR__ . '/../tpl/hbciprofiles.tpl');
    }

    public function getVOPBelege()
    {
        $aktionsbtn = ['cssclass' => 'btn btn-info btn-sm fancyboxfullscreen', 'text' => 'Belege Öffnen', 'data-fancy-href' => DC_SCRIPT . '?switchTo=getBeleg&fancy=1&doctype=pdf&doc='];

        $this->listView = new listView($aktionsbtn, $menubtn, false); // @todo undefined $menubtn!

        $this->listView->sessName = 'belege';
        $this->listView->getCurrentOrder();

        $limitfilter = [$this->listView->startCount, $this->listView->maxCount];

        $dataType = $this->dataTypes->getBelege($this->listView->startCount,$this->listView->maxCount,
                                ['column' => $this->listView->order, 'direction' => $this->listView->orderDir], $this->listView->filter, $this->listView->fieldModes);

        $this->listView->columns = $dataType['order'];

        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);

        $this->listView->createUnsortedList($res, $dataType['count']);
        $listview = ['header' => $this->listView->createListViewHeader(DC_SCRIPT, 'V.O.P Belege'), 'table' => $this->listView->createList()];

        $this->View('listview', $listview);

        return $this->smarty->fetch(__DIR__ . '/../tpl/belege.tpl');
    }

    public function getDocumentation()
    {
        try {
            $handle = md5('D3B!7C0NN3CT_' . date('Ymd') . 'AUTH_TOKEN');
            $soap = $this->API->mahnwesen();
            $musterList = $soap->getMusterArt($handle);
            $this->View('musterList', $musterList);
            if (DC()->hasvalue('art')) {
                $musterArt = $soap->getMusterList($handle, DC()->get('art'));
                $this->View('musterArt', $musterArt);
            }

            return $this->smarty->fetch(__DIR__ . '/../tpl/documentation.tpl');
        } catch (Exception $e) {
            return 'Temporäre Störung : ' . $e->getMessage();
        }
    }

    public function fetchTemplate($id)
    {
        switch ($id) {
            case 'documentation':
            return $this->getDocumentation();
            break;
            case 'belege':
            return $this->getVOPBelege();
            break;
            case 'hbciProfiles':
            return $this->hbciProfiles();
            break;
            case 'bonigateway':
            return $this->BoniGatewaySettings();
            break;
            case 'BoniLog':
            return $this->BoniGatewayLog();
            break;
            case 'opListe':
            return $this->getOPListe();
            break;
            case 'dtaList':
            return $this->getDTAList();
            break;
            case 'zakontrolle':
            return $this->zahlungsabgleichManuell();
            break;
            case 'zahlungsabgleich':
            return $this->Zahlungsabgleich();
            break;
            case 'settings':
            return $this->pageUserSettings(DC()->get('setting'));
            break;
            case 'logout':
            $this->Log('Abmeldung', 'Benutzerabmeldung von ' . $_SERVER['REMOTE_ADDR']);
            $tpl = 'LOGOUT';
            break;
            case 'start':
            return $this->getDashBoard();
            break;
            case 'zalog':
            return $this->zaLOG();
            break;
            case 'logbuch':
            return $this->logbuch();
            break;
            case 'detailansicht':
            return $this->akteneinsicht();
            break;
            default:
            return $this->getListView($id);
            break;
        }
    }

    public function getDashBoard()
    {
        $rs = $this->db->getSQLResults("SELECT dc_cronlog.*,DATE_FORMAT(dAction,'%d.%m.%Y') as dtDay,s_order.ordernumber  from dc_cronlog LEFT OUTER JOIN s_order on s_order.id = pkOrder order by id DESC ");
        $logEntrys = [];
        foreach ($rs as $row) {
            $logEntrys[$row['dtDay']][] = $row;
            if ($row['bIserror'] == 1) {
                $this->View('CRONJOB_ERROR_MSG', 'Es wurden Fehler im Cronjob festgestellt');
            }
        }
        if (count($logEntrys) > 0) {
            $this->smarty->assign('cronjob_log', $logEntrys);
        }
        $currentLog = $this->get('currentLog');
        if ($currentLog == null) {
            $currentLog = date('d.m.Y');
        }

        if ($currentLog === date('d.M.Y') && count($logEntrys[$currentLog]) === 0) {
            $this->View('CRONJOB_ERROR_MSG', 'Cronjob wurde heute noch nicht ausgeführt');
        }
        $this->smarty->assign('currentLog', $logEntrys[$currentLog]);

        return $this->smarty->fetch(__DIR__ . '/../tpl/dashboard.tpl');
    }

    public function akteneinsicht()
    {
        $this->lastSelected = (int) DC()->get('id');
        $soap = $this->API->mahnwesen();
        $res = $this->db->singleResult('SELECT pkOrder from dc_auftrag where id = ' . (int) DC()->get('id'));
        $pkOrder = $res['pkOrder'];

        if (DC()->hasvalue('nachrichtsb')) {
            $msg = $soap->insertMSG($this->settings->registration['vopUser'], md5($this->settings->registration['vopToken']), '', (int) $pkOrder, DC()->get('nachrichtsb'));
            if ($msg->Error === 'OK') {
                $this->View('SUCCESS_MSG', 'Die Nachricht wurde übermittelt');
            } else {
                $this->View('ERROR_MSG', 'Die Nachricht konnte nicht übermittelt werden');
            }
        }

        try {
            $schuldner = $soap->getSchuldner($this->settings->registration['vopUser'], md5($this->settings->registration['vopToken']), '', (int) $pkOrder);
            if ($schuldner->Error !== 'Error') {
                $this->View('schuldner', $schuldner);
            } else {
                $this->View('API_ERROR', 'Daten Konnten nicht abgerufen werden');
            }

            $lea = $soap->getLEA($this->settings->registration['vopUser'], md5($this->settings->registration['vopToken']), '', (int) $pkOrder);
            if (count($lea) > 0) {
                $this->View('lea', $lea);
            } else {
                $this->View('API_ERROR', 'Daten Konnten nicht abgerufen werden');
            }

            $vbdaten = $soap->getVBDaten($this->settings->registration['vopUser'], md5($this->settings->registration['vopToken']), '', (int) $pkOrder);
            if ($vbdaten->Error !== 'Error') {
                $this->View('vbdaten', $vbdaten);
            } else {
                $this->View('API_ERROR', 'Daten Konnten nicht abgerufen werden');
            }

            $fkto = $soap->getFKTO($this->settings->registration['vopUser'], md5($this->settings->registration['vopToken']), '', (int) $pkOrder);
            if ($fkto->Error !== 'Error') {
                $_fkto['hauptforderung'] = ['soll' => number_format(round($fkto->Hauptforderung, 2), 2, ',', '.'),
                    'haben' => number_format(round($fkto->ZEaufHaupt, 2), 2, ',', '.'),
                    'saldo' => number_format((round($fkto->Hauptforderung, 2) - round($fkto->ZEaufHaupt, 2)), 2, ',', '.'), ];

                $_fkto['zinsen'] = ['soll' => number_format(round($fkto->Zinsen, 2), 2, ',', '.'),
                    'haben' => number_format(round($fkto->ZEaufZinsen, 2), 2, ',', '.'), 'saldo' => number_format((round($fkto->Zinsen, 2) - round($fkto->ZEaufZinsen, 2)), 2, ',', '.'), ];

                $_fkto['ra'] = ['soll' => number_format(round($fkto->Kostenverzinschlich, 2), 2, ',', '.'),
                    'haben' => number_format(round($fkto->ZEaufKostenVerzinslich, 2), 2, ',', '.'),
                    'saldo' => number_format((round($fkto->Kostenverzinslich, 2) - round($fkto->ZEaufKostenVerzinslich, 2)), 2, ',', '.'), ];

                $_fkto['kosten'] = [
                    'soll' => number_format(round($fkto->Kosten, 2), 2, ',', '.'), 'haben' => number_format(round($fkto->ZEaufKosten, 2), 2, ',', '.'), 'saldo' => number_format((round($fkto->Kosten, 2) - round($fkto->ZEaufKosten, 2)), 2, ',', '.'), ];
                $haben = number_format((round($fkto->ZEaufHaupt + $fkto->ZEaufZinsen + $fkto->ZEaufKostenVerzinslich + $fkto->ZEaufKosten, 2)), 2, ',', '.');

                $_fkto['salden'] = ['soll' => number_format(round($fkto->Hauptforderung, 2) + round($fkto->Zinsen, 2) + round($fkto->Kostenverzinslich, 2) + round($fkto->Kosten, 2), 2, ',', '.'),
                    'haben' => $haben,
                    'saldo' => number_format((round($fkto->Hauptforderung, 2) - round($fkto->ZEaufHaupt, 2)) + (round($fkto->Zinsen, 2) - round($fkto->ZEaufZinsen, 2)) + (round($fkto->Kostenverzinslich, 2) - round($fkto->ZEaufKostenVerzinslich, 2)) + (round($fkto->Kosten, 2) - round($fkto->ZEaufKosten, 2)), 2, ',', '.'), ];

                $this->View('fkto', $_fkto);
            } else {
                $this->View('API_ERROR', 'Daten Konnten nicht abgerufen werden');
            }
        } catch (Exception $e) {
            $this->View('API_ERROR', 'Daten Konnten nicht abgerufen werden');
            $this->LOG('Akteneinsicht', $e, 10);
        }

        return $this->smarty->fetch(__DIR__ . '/../tpl/detailansicht.tpl');
    }

    public function zaLOG()
    {
        if (DC()->hasvalue('removeUmsatz') && DC()->hasvalue('cbx')) {
            foreach (DC()->get('cbx') as $key => $val) {
                $this->hbci->removeUmsatz((int) $val);
            }
        }
        if ($this->zalogdate == null) {
            $this->zalogdate = date('d.m.Y');
        }
        if (DC()->hasvalue('datefilter')) {
            $this->zalogdate = DC()->get('datefilter');
        }
        $dt = new DateTime($this->zalogdate);
        $dt = $dt->format('d.m.Y');
        $this->View('datefilter', $dt);
        //DC()->get('datefilter') = $dt;
        $aktionsbtn = false;
        $this->listView = new listView($aktionsbtn, __DIR__ . '/../tpl/btn/zalog.tpl', true);
        $this->listView->sessName = 'zalog';
        $this->listView->getCurrentOrder();
        $dataType['order'] = [
                'PK' => [false, 'umsatz.kUmsatz', 'Key', false],
                'name' => [true, 'umsatz.cName', 'Name', true],
                'dBuchung' => [true, 'umsatz.dBuchung', 'Datum ', false],
                'wert' => [true, 'umsatz.fWert', 'Betrag', false],
                'cVzweck' => [true, 'umsatz.cVzweck', 'Verwendungszweck', true],
                'sumGesamt' => [false, 'fWert', 'Betrag', false],
            ];

        $this->listView->columns = $dataType['order'];
        $dataType['query'] = "SELECT umsatz.kUmsatz ,umsatz.cName,DATE_FORMAT(umsatz.dBuchung,'%d.%m.%Y') as dBuchung,CAST(umsatz.fWert AS DECIMAL(12,2)) as fWert,umsatz.cVzweck,umsatz.kUmsatz as id  from dc_umsatz as umsatz where (nVerbucht = 1 or nNichtverbuchen = 1) ";
        $setFilter = false;
        if (DC()->hasvalue('setFilter')) {
            foreach (DC()->get('setFilter') as $key => $value) {
                if ($value != '') {
                    $setFilter = true;
                    $dataType['query'] .= " AND $key LIKE '%" . DC()->db->dbEscape($value) . "%' ";
                }
            }
        }

        if ($setFilter == false) {
            $dataType['query'] .= "	AND  DATE_FORMAT(umsatz.dAbgleich,'%d.%m.%Y') = '$dt' ";
        }

        $dataType['query'] .= ' ORDER BY ' . $this->listView->order . ' ' . $this->listView->orderDir;
        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);
        $this->listView->createUnsortedList($res, count($res));
        $listview = ['header' => $this->listView->createListViewHeader(DC_SCRIPT, 'Zahlungsabgleich - Protokoll'), 'table' => $this->listView->createList()];
        $this->View('listview', $listview);

        return $this->smarty->fetch(__DIR__ . '/../tpl/zalog.tpl');
    }

    public function logbuch()
    {
        $this->listView = new listView($aktionsbtn, $menubtn); // @todo fix undefined $aktionsbtn, $menubtn!
        $this->listView->sessName = 'logbuch';
        $this->listView->getCurrentOrder();

        $limitfilter = [$this->listView->startCount, $this->listView->maxCount];

        $dataType = $this->dataTypes->getLogBuch($this->listView->startCount,$this->listView->maxCount,
                                ['column' => $this->listView->order, 'direction' => $this->listView->orderDir], $this->listView->filter, $this->listView->fieldModes);

        $this->listView->columns = $dataType['order'];

        $this->listView->setFilter(DC()->get('setFilter'));
        $res = $this->db->getSQLResults($dataType['query']);
        $this->listView->createUnsortedList($res, $dataType['count']);
        $listview = ['header' => $this->listView->createListViewHeader(DC_SCRIPT, 'Logbuch'), 'table' => $this->listView->createList()];

        $this->View('listview', $listview);

        return $this->smarty->fetch(__DIR__ . '/../tpl/logbuch.tpl');
    }

    public function addSteuerDatei()
    {
    }

    public function zahlungsabgleichManuell()
    {
        DC()->dataTypes->getZahlungsabgleichBestellungen();
        $SteuerDateifromSoap = '';

        $vopUmsatz = false;

        $transaction = DC()->get('transaction');
        if (DC()->hasvalue('selectedDTA') && DC()->get('selectedDTA') > 0) {
            DC()->hbci->matchDTA($transaction, DC()->get('selectedDTA'));
        }
        $umsatz = DC()->hbci->umsaetze[$transaction];

        $this->lastSelected = $transaction;

        if (DC()->hasvalue('addbestellung') && count(DC()->get('addbestellung')) > 0) {
            foreach (DC()->get('addbestellung') as $addMatch) {
                DC()->hbci->addMatching((int)$transaction, (int) $addMatch);
                DC()->hbci->matches[$transaction]['selected'] = (int) $addMatch;
            }
        }

        if (DC()->hasvalue('submitaction')) {
            if (DC()->get('submitaction') === 'setVerbuchen') {
                DC()->hbci->matches[$transaction]['verbuchen'] = (DC()->get('verbuchen')) ? true : false;
            }
            if (DC()->hasvalue('changeselected')) {
                DC()->hbci->matches[$transaction]['selected'] = DC()->get('changeselected');
            }

            $match = DC()->hbci->matches[$transaction]['pos'][DC()->get('pkOrder')];
            if (DC()->hasvalue('change')) {
                if ($match->richtung === '+') {
                    if ($match->Offen < 0) {
                        $match->Zahlbetrag = '0.00';
                    } else {
                        $match->Zahlbetrag = DC()->get('zahlbetrag') > $match->Offen ? $match->Offen : DC()->get('zahlbetrag');
                    }
                    $match->Ueberzahlung = DC()->get('Ueberzahlung');
                    $match->skonto = DC()->get('skonto');
                    $match->mahnkosten = DC()->get('mahnkosten');
                } elseif ($match->richtung === '-') {
                    $match->bankruecklast = DC()->get('bankruecklast');
                    $match->bankruecklastkosten = DC()->get('bankruecklastkosten');
                    $match->gutschrift = DC()->get('gutschrift');
                    $match->erstattung = DC()->get('erstattung');
                }
                DC()->hbci->setSumMatches($umsatz);
            }
            if (DC()->get('delete')) {
                $match->zugeordnet = false;
            }
            if (DC()->hasvalue('add')) {
                DC()->hbci->matches[$transaction]['pos'][DC()->get('changeselected')]->zugeordnet = true;
            }
            DC()->hbci->matches[$transaction]['pos'][DC()->get('pkOrder')] = $match;
        }

        if ($umsatz['cName'] != '' && stripos($umsatz['cName'], 'V.O.P') !== false) {
            $vopUmsatz = true;
            $explodeSVWZ = explode(' ', $umsatz['cVzweck']);
            if (strlen($explodeSVWZ[0]) === 19) {
                try {
                    $soap = $this->API->mahnwesen();
                    $soapResponse = $soap->getPaymentFile($this->settings->registration['vopUser'], md5($this->settings->registration['vopToken']), $explodeSVWZ[0], $umsatz['fWert']);
                    if ($soapResponse->Error === 'OK') {
                        $SteuerDateifromSoap = base64_decode($soapResponse->bFile);
                    }
                } catch (Exception $e) {
                }
            }
        }
        // STEUERDATEI VOP
        if ((DC()->get('submitaction') !== 'setVerbuchen' && strlen($SteuerDateifromSoap) > 10) || (DC()->hasvalue('submitSteuerDatei') && isset($_FILES['steuerdatei']))) {
            if (strlen($SteuerDateifromSoap) > 10) {
                $xml = simplexml_load_string($SteuerDateifromSoap);
            } else {
                $getFile = file_get_contents($_FILES['steuerdatei']['tmp_name']);
                $xml = simplexml_load_string($getFile);
            }
            $countSumSteuerdatei = (float) 0.00;
            $fVorsteuerGesamt = (float) 0.00;
            foreach ($xml->rechnung as $rechnung) {
                $countSumSteuerdatei += (float) $rechnung->fZahlbetrag;
                $fVorsteuerGesamt += (float) $rechnung->fVorsteuer;
            }

            DC()->hbci->matches[$transaction] = [];
            DC()->hbci->matches[$transaction]['fVorsteuerGesamt'] = $fVorsteuerGesamt;

            if (number_format($umsatz['fWert'], 2) !== number_format($countSumSteuerdatei, 2)) {
                $this->View('SUM_MISSMATCH', $countSumSteuerdatei);
            }
            foreach ($xml->rechnung as $rechnung) {
                $maxBetrag = (float) $rechnung->fZahlbetrag + (float) $rechnung->fVorsteuer - (float) $rechnung->fMahnkosten;
                $pkOrderArr = explode(',', (string) $rechnung->kRechnung);
                $transactionCount = 1;
                foreach ($pkOrderArr as $pkOrder) {
                    if (DC()->hbci->bestellungen[$pkOrder] != null) {
                        $matching = [];
                        $betragOffen = (float) DC()->hbci->bestellungen[$pkOrder]['offen'];
                        $sumBuchen = $maxBetrag;
                        $fVorsteuer = (float) $rechnung->fVorsteuer;
                        $fZahlbetrag = (float) $rechnung->fZahlbetrag;
                        $fMahnkosten = (float) $rechnung->fMahnkosten;

                        if ($sumBuchen > 0 && $betragOffen > 0 && count($pkOrderArr) > 1 && $sumBuchen > $betragOffen) {
                            $sumBuchen = $betragOffen;
                            $maxBetrag -= $sumBuchen;
                        }

                        if ($transactionCount == 1 && ($fMahnkosten > 0 || $fMahnkosten < 0)) {
                            $matching['mahngeb'] = $fMahnkosten;
                        }
                        $matching['steuererstattung'] = $fVorsteuer;
                        $matching['fWert'] = $sumBuchen;
                        $beleg = ['RechJahr' => (string) $rechnung->nRechJahr, 'RechNr' => (string) $rechnung->nRechNr];

                        //DC()->hbci->matches[$umsatz["kUmsatz"]]['pos'][$pkOrder] = new buchungsPos($pkOrder,true,$umsatz,DC()->hbci->bestellungen[$pkOrder],$matching,null,true,$beleg);
                        $arrPos = count(DC()->hbci->matches[$umsatz['kUmsatz']]['pos']) + 1;
                        DC()->hbci->matches[$umsatz['kUmsatz']]['pos'][$arrPos] = new BuchungsPos($pkOrder, true, $umsatz, DC()->hbci->bestellungen[$pkOrder], $matching, null, true, $beleg);
                        $maxBetrag = number_format($maxBetrag - $sumBuchen, 2);
                    }

                    ++$transactionCount;
                }
            }
        }
        // STEUERDATEI VOP
        $this->View('selectedBestellung', DC()->hbci->matches[$transaction]['selected']);

        $umsatz['zugeordnetvalue'] = DC()->hbci->returnCountBuchungPos($umsatz);
        if ($vopUmsatz) {
            $umsatz['zugeordnetvalue']['value'] = number_format($umsatz['zugeordnetvalue']['value'] - DC()->hbci->matches[$transaction]['fVorsteuerGesamt'], 2, '.', '');
        }

        $differenz = $umsatz['nType'] == 1 ? ($umsatz['fWert'] * -1) - $umsatz['zugeordnetvalue']['value'] : ($umsatz['fWert']) - $umsatz['zugeordnetvalue']['value'];

        if ($differenz == '0' && $vopUmsatz) {
            $umsatz['zugeordnetvalue']['action'] = true;
            $umsatz['zugeordnetvalue']['class'] = 'success';
        }

        if ($differenz == '0' && DC()->hasvalue('change')) {
            //DC()->get('verbuchen') = true;
            DC()->hbci->matches[$transaction]['verbuchen'] = DC()->hasvalue('verbuchen');
        }

        $this->View('differenz', number_format($differenz, 2, '.', ''));
        $this->View('verbuchen', DC()->hbci->matches[$transaction]['verbuchen']);
        $this->View('verbucht', DC()->hbci->matches[$transaction]['verbucht']);
        $this->View('umsatz', $umsatz);
        $this->View('debitconnectstatus', $this->getVOPStatusText(DC()->hbci->matches[$transaction]['selected']));
        $this->View('buchungsPos', DC()->hbci->matches[$transaction]['pos']);

        if (!$vopUmsatz) {
            $dt = new DateTime($umsatz['datum']);
            $dtEnd = $dt->format('Y-m-d');
            $dt->modify('-14 day');
            $dtStart = $dt->format('Y-m-d');
            $dtaListQuery = "SELECT dateCreated,idTransaktion,nAnzahl FROM `dc_dtacreatelog` WHERE `nType` = 0 AND kUmsatz = 0  AND `dateCreated` > '" . $dtStart . "' and dateCreated < '" . $dtEnd . "' and fSumme = '" . $umsatz['fWert'] . "'";
            $dtaList = DC()->db->getSQLResults($dtaListQuery, false);
            if (count($dtaList) > 0) {
                $this->View('dtaList', $dtaList);
            }
        }

        if ($vopUmsatz) {
            return $this->smarty->fetch(__DIR__ . '/../tpl/zuordnungVOP.tpl');
        }

        return $this->smarty->fetch(__DIR__ . '/../tpl/zuordnung.tpl');
    }

    public function getVOPStatusText($pkOrder)
    {
        $q = DC()->db->singleResult('SELECT IFNULL(VOPStatus,0) as vopstatus from dc_auftrag where pkOrder = ' . (int) $pkOrder);
        $txt = '';
        switch ((int) $q['vopstatus']) {
            case 39:
            $txt = 'Zahlungserinnerung versendet';
            break;
            case 55:
            $txt = 'Mahnservice in Bearbeitung';
            break;
            case 59:
            $txt = 'Mahnservice erledigt';
            break;
            case 95:
            $txt = 'Inkasso in Bearbeitung';
            break;
            case 99:
            $txt = 'Inkasso erledigt';
            break;
            default:
            $txt = '';
            break;
        }

        return $txt;
    }

    public function parseHTMLTemplate($html)
    {
        $html = str_replace('<style', '{literal}<style', $html);
        $html = str_replace('</style>', '</style>{/literal}', $html);

        return $html;
    }

    public function sendZahlungseingang($pkOrder, $type = 1)
    {
        if ($this->settings->currentHBCI['bestaetigung'] == 0) {
            return;
        }

        if ($type >= $this->settings->currentHBCI['bestaetigung']) {
            try {
                $params = $this->dataTypes->getZEVars((int) $pkOrder);

                if (in_array($params['KundenGruppeId'], $this->settings->hbciMailCustomerGroupDisable)) {
                    return;
                }
                foreach ($params as $key => $value) {
                    $this->View($key, $value);
                }
                DC()->dataTypes->assignTemplateVars($pkOrder);
                $tpl = $this->getConf('tpl_zahlungseingang', base64_encode(file_get_contents(__DIR__ . '/../tpl/bestaetigung.tpl')));
                $tpl = $this->parseHTMLTemplate(base64_decode($tpl));

                $htmlEMAIL = $this->smarty->fetch('string:' . $tpl . '');
                $email = $this->dataTypes->getOrderEmail($pkOrder);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->Log('E-Mail', "Ungueltige E-Mail $email", 0, $pkOrder);

                    return;
                }
                $this->mailer = Shopware()->Mail();
                $this->mailer->clearFrom();
                $this->mailer->clearRecipients();
                $this->mailer->setParts([]);
                $this->mailer->setFrom($this->settings->currentHBCI['absender']);
                $this->mailer->addAddress($this->dataTypes->getOrderEmail($pkOrder));
                $this->mailer->Subject = $this->settings->currentHBCI['betreff'];

                $this->mailer->isHTML(true);
                if (strlen($params['attachment']) > 100) {
                    $this->mailer->createAttachment(
                        base64_decode($params['attachment']), Zend_Mime::TYPE_OCTETSTREAM, Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, 'Rechnung' . $params['RechnungsNr'] . '.pdf'
                    );
                }
                $this->mailer->Body = $htmlEMAIL;
                if ($this->mailer->send()) {
                    $this->Log('E-Mail', 'Zahlungseingangsmail wurde versendet', 0, $pkOrder);
                } else {
                    $this->Log('E-Mail', 'Zahlungseingangsmail konnte nicht versendet werden', 0, $pkOrder);
                }
            } catch (Exception $e) {
                $this->Log('E-Mail', 'Zahlungseingangsmail konnte nicht versendet werden' . $e->getMessage(), 0, $pkOrder);
            }
        }
    }

    public function sendZahlungserinnerung($pkOrder, $vorschau)
    {
        if (DC()->settings->currentSetting->shopwareapibenutzen == 0 && !$vorschau) {
            return false;
        }
        $this->setVOPAuftragDetail($pkOrder);
        $tpl = null;
        $params = $this->dataTypes->getZEVars((int) $pkOrder);
        foreach ($params as $key => $value) {
            $this->View($key, $value);
        }
        DC()->dataTypes->assignTemplateVars($pkOrder);
        switch ($this->settings->currentSetting->zeArt) {
            case '1':
            $tpl = $this->getConf('tpl_zahlungserinnerung', base64_encode(file_get_contents(__DIR__ . '/../tpl/standardtemplate.tpl')));
            break;
            default:
            $tpl = templates::vopTPL();
            break;
        }
        $tpl = $this->parseHTMLTemplate(base64_decode($tpl));
        //if($vorschau) $tpl.="{debug}";
        $htmlEMAIL = $this->smarty->fetch('string:' . $tpl . '');

        if (!$vorschau) {
            $this->mailer = Shopware()->Mail();
            $this->mailer->clearFrom();
            $this->mailer->clearRecipients();
            $this->mailer->setParts([]);
            $this->mailer->setFrom($this->settings->currentSetting->smtpabsender);
            $this->mailer->addAddress($this->dataTypes->getOrderEmail($pkOrder));
            $this->mailer->Subject = $this->settings->currentSetting->smtpbetreff;
            $this->mailer->addBCC($this->settings->currentSetting->smtpkopie);

            $this->mailer->isHTML(true);
            if (strlen($params['attachment']) > 100) {
                $this->mailer->createAttachment(
                        base64_decode($params['attachment']), Zend_Mime::TYPE_OCTETSTREAM, Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, 'Rechnung' . $params['RechnungsNr'] . '.pdf'
            );
                //$this->mailer->addStringAttachment(base64_decode($params["attachment"]), 'Rechnung'.$params['RechnungsNr'].'.pdf');
            }
            //$this->mailer->addStringAttachment(base64_decode($params["attachment"]), 'Rechnung'.$params['RechnungsNr'].'.pdf');
            $this->mailer->Body = $htmlEMAIL;
            if ($this->mailer->send()) {
                if ($this->setVOPAuftrag(39, $params['id'])) {
                    $this->dataTypes->changeOrder($pkOrder, null, $this->settings->currentSetting->statusZE, 0);
                    $this->Log('Zahlungserinnerung', 'Zahlungserinnerung Versendet', 0, $pkOrder);

                    return true;
                }
            } else {
                $this->View('MAIL_ERROR', $this->mailer->ErrorInfo);
            }
        }

        return $htmlEMAIL;
    }

    public function zaSuche()
    {
        DC()->dataTypes->getZahlungsabgleichBestellungen();

        if (DC()->hasvalue('dta')) {
            $transaction = DC()->get('transaction');
            $umsatz = DC()->hbci->umsaetze[$transaction];

            $dt = new DateTime($umsatz['datum']);
            $dtEnd = $dt->format('Y-m-d');
            $dt->modify('-14 day');
            $dtStart = $dt->format('Y-m-d');

            $dtaListQuery = "SELECT dateCreated,idTransaktion,nAnzahl FROM `dc_dtacreatelog` WHERE `nType` = 0 AND kUmsatz = 0 and `dateCreated` > '" . $dtStart . "' and dateCreated < '" . $dtEnd . "' and fSumme = '" . $umsatz['fWert'] . "'";
            $dtaList = DC()->db->getSQLResults($dtaListQuery, false);

            if (count($dtaList) > 0) {
                $this->View('dtaList', $dtaList);
            }
            $searchres = [];
            if (DC()->hasvalue('selectedDTA') && DC()->get('selectedDTA') > 0) {
                $sqlRs = DC()->db->getSQLResults('SELECT pkOrder from dc_dtacreatelog where idTransaktion = ' . (int) DC()->get('selectedDTA') . ' and nType = 1', false);
                foreach ($sqlRs as $row) {
                    $searchres[] = DC()->hbci->bestellungen[$row['pkOrder']];
                }
                $this->View('searchres', $searchres);
            }
        }
        if (DC()->hasvalue('suchebestellung')) {
            $searchres = [];
            $directSearch = explode(';', DC()->get('searchfield'));
            foreach (DC()->hbci->bestellungen as $bestellung) {
                if (DC()->hasvalue('limit') && DC()->get('limit') === 'open' && $bestellung['offen'] <= 0) {
                    continue;
                }

                $searchfield = strtoupper(DC()->get('searchfield'));
                if (count($directSearch) > 0 && in_array($bestellung['id'], $directSearch)) {
                    $searchres[] = $bestellung;
                } elseif (strtoupper($bestellung['RechnungsNr']) === $searchfield) {
                    $searchres[] = $bestellung;
                } elseif (strtoupper($bestellung['ordernumber']) === $searchfield) {
                    $searchres[] = $bestellung;
                } elseif (strtoupper($bestellung['firstname']) === $searchfield) {
                    $searchres[] = $bestellung;
                } elseif (strtoupper($bestellung['lastname']) === $searchfield) {
                    $searchres[] = $bestellung;
                } elseif (strtoupper($bestellung['KundenNr']) === $searchfield) {
                    $searchres[] = $bestellung;
                }
            }
            $this->View('searchres', $searchres);
        }

        return $this->smarty->fetch(__DIR__ . '/../tpl/suchebestellung.tpl');
    }

    public function getBelegPDF($doc)
    {
        $doc = (int) $doc;
        if (DC()->get('doc') > 0) {
            $rs = DC()->db->singleResult('select dc_rechdoc.bDocument from dc_rechnung left join dc_rechdoc on dc_rechdoc.kLaufNr = dc_rechnung.kLaufnr where dc_rechnung.id = ' . $doc);
            $download = "<a download='VOPRechnung-" . $doc . ".pdf' href='data:application/pdf;base64," . $rs['bDocument'] . "' title='Download pdf document' />Download PDF</a>";
            $embed = "$download<embed src='data:application/pdf;base64," . $rs['bDocument'] . "' width='100%' height='100%' alt='pdf' pluginspage='http://www.adobe.com/products/acrobat/readstep2.html' type='application/pdf'>";
        }

        return $embed;
    }

    public function getLEADoc()
    {
        $soap = $this->API->mahnwesen();
        $lea = $soap->getLEA();

        try {
            $doc = $soap->getLEAdoc($this->settings->registration['vopUser'], md5($this->settings->registration['vopToken']), (int) DC()->get('doc'));
            $download = "<a download='" . DC()->get('doc') . ".pdf' href='data:application/pdf;base64," . $doc->document . "' title='Download pdf document' />Download PDF</a>";
            $embed = "$download<embed src='data:application/pdf;base64," . $doc->document . "' width='100%' height='100%' alt='pdf' pluginspage='http://www.adobe.com/products/acrobat/readstep2.html' type='application/pdf'>";
        } catch (Exception $e) {
            $this->View('API_ERROR', $e);
        }

        return $embed;
    }

    public function hbcirequestmanuell()
    {
        $profiles = $this->settings->hbciProfiles;
        $active = count($profiles) > 0;
        if ($active) {
            $this->View('profiles', $profiles);
        }

        return $this->smarty->fetch(__DIR__ . '/../tpl/hbcirequest.tpl');
    }

    public function syncFancy()
    {
        $this->getSyncList();
        $this->View('synccounter', count($this->syncList));

        return $this->smarty->fetch(__DIR__ . '/../tpl/sync.tpl');
    }

    public function setMahnstop()
    {
        if (DC()->hasvalue('changeMahnstop')) {
            $_date = DC()->get('bis');
            if (DC()->hasvalue('pkCustomer')) {
                $pkCustomer = (int) DC()->get('pkCustomer');
                $entry = new stdClass();
                $entry->pk = $pkCustomer;
                $entry->cCommentary = DC()->get('cCommentary');
                $entry->nType = 1;
                if (strlen($_date) > 3) {
                    try {
                        $dt = new dateTime($_date);
                        $entry->resetDate = $dt->format('Y-m-d');
                    } catch (Exception $e) {
                    }
                }

                if (DC()->hasvalue('addMahnstopCustomer')) {
                    DC()->db->dbInsert('dc_mahnstop', $entry);
                } elseif (DC()->hasvalue('removeMahnstopCustomer')) {
                    DC()->db->dbQuery(' DELETE FROM dc_mahnstop where nType = ' . $entry->nType . ' and pk = ' . $pkCustomer);
                }
            }
            if (DC()->hasvalue('pkOrder')) {
                $pkOrder = (int) DC()->get('pkOrder');
                $entry = new stdClass();
                $entry->pk = $pkOrder;
                $entry->cCommentary = DC()->get('cCommentary');
                $entry->nType = 0;
                if (DC()->hasvalue('addMahnstopOrder')) {
                    if (strlen($_date) > 3) {
                        try {
                            $dt = new dateTime($_date);
                            $entry->resetDate = $dt->format('Y-m-d');
                        } catch (Exception $e) {
                        }
                    }
                    DC()->db->dbInsert('dc_mahnstop', $entry);
                } elseif (DC()->hasvalue('removeMahnstopOrder')) {
                    DC()->db->dbQuery(' DELETE FROM dc_mahnstop where nType = ' . $entry->nType . ' and pk = ' . $pkOrder);
                }
            }
        }
    }

    public function getOverViewGateway($pkCustomer)
    {
        if ($this->boniGateway == null) {
            $this->boniGateway = new DebitConnect_BoniGateway();
        }
        if (DC()->get('requestLogin') && DC()->get('gatewaylogin') && DC()->get('gatewaypass')) {
            $this->boniGateway->checkLoginGateway(DC()->get('gatewaylogin'), DC()->get('gatewaypass'));
        }
        if ($this->boniGateway->logged_in) {
            $lastInvoiceAdress = $this->dataTypes->BoniGatewayAdresses($pkCustomer);

            $this->View('gateway_invoice_address', $lastInvoiceAdress);
            $this->View('countries', $this->dataTypes->getCountryISO());
            $this->View('projecte_b2c', $this->boniGateway->projecte_b2c);
            $this->View('projecte_b2b', $this->boniGateway->projecte_b2b);
            $this->View('kennziffern', $this->boniGateway->kennziffern);
            if (DC()->get('getRequestBoniGateway')) {
                $request = $this->boniGateway->getRequest();
                if (array_key_exists('ergebnis', $request)) {
                    $this->View('GatewayResult', $request['response']);
                } elseif (array_key_exists('trefferliste', $request)) {
                    $this->View('GatewayList', $request['response']);
                }
            }

            $history = $this->dataTypes->BoniGatewayHistory($pkCustomer);
            $this->View('gateway_history', $history);
        }

        $this->View('gatewaylogin', $this->boniGateway->logged_in);

        return $this->smarty->fetch(__DIR__ . '/../tpl/gatewayCustomer.tpl');
    }

    public function getOverView($pkOrder, $setmahnstop = true)
    {
        $this->setMahnstop();
        $payments = DC()->hbci->getPaymentDetail((int) $pkOrder);
        $this->View('paymentscount', count($payments));
        $zeVars = $this->dataTypes->getZEVars((int) $pkOrder);
        $mahnstop = DC()->db->getSQLResults("SELECT * from dc_mahnstop where nType = 0 and pk = $pkOrder OR nType = 1 and pk = " . $zeVars['pkCustomer']);
        $mahnstopCustomerGroup = $this->settings->mahnstopCustomerGroup;

        if (count($mahnstopCustomerGroup) > 0 && in_array($zeVars['KundenGruppeId'], $mahnstopCustomerGroup)) {
            $this->View('mahnstopCustomerGroup', 'Mahnstop über Kundengruppe ' . $zeVars['KundenGruppeName'] . ' gesetzt');
        }
        $_mahnstopp = [];
        foreach ($mahnstop as $mahnstopitem) {
            $_mahnstopp[$mahnstopitem['nType']] = $mahnstopitem;
        }

        /*
        if(count($mahnstop)>0)
        {
            $values = array();
            foreach($mahnstop as $lvl)
            {
                if($lvl["nType"] == 0){
                     $values["order"] = $lvl["pk"];
                }
                if($lvl["resetDate"]){
                    $dt = new dateTime($lvl["resetDate"]);
                    $values["resetDate"] = $dt->format("d.m.Y");
                }
                if($lvl["nType"] == 1) $values["customer"] = $lvl["pk"];
                $values["cCommentary"] = $lvl["cCommentary"];
            }
            $this->View("mahnstop",$values);
        }
        */
        $this->View('mahnstop', $_mahnstopp);
        $rs = DC()->db->singleResult('SELECT IFNULL(VOPStatus,0) as mahnwesenstatus,dc_status.fGesamt,dc_status.fOffen from dc_auftrag left OUTER join dc_status on dc_status.pkOrder = dc_auftrag.pkOrder where dc_auftrag.pkOrder = ' . (int) $pkOrder);
        $mahnwesenstatus = '';
        if ($rs['fOffen']) {
            $this->View('fOffenVOP', $rs['fOffen']);
        }
        if ($rs['fGesamt']) {
            $this->View('fGesamtVOP', $rs['fGesamt']);
        }
        switch ($rs['mahnwesenstatus']) {
            case '39':
            $mahnwesenstatus = 'Zahlungserinnerung versendet';
            break;
            case '55':
            $mahnwesenstatus = 'Mahnung in Bearbeitung';
            $setmahnstop = false;
            break;
            case '59':
            $mahnwesenstatus = 'Mahnung Erledigt';
            $setmahnstop = false;
            break;
            case '95':
            $mahnwesenstatus = 'Inkasso in Bearbeitung';
            $setmahnstop = false;
            break;
            case '99':
            $mahnwesenstatus = 'Inkasso Erledigt';
            $setmahnstop = false;
            break;
            default:
            break;
        }
        $this->View('payedMahngeb', DC()->hbci->checkPaymentMahngebuehr($pkOrder));
        $this->View('setmahnstop', $setmahnstop);
        $this->View('mahnwesenstatus', $mahnwesenstatus);
        $this->View('orderData', $zeVars);
        $this->View('payments', $payments);

        return $this->smarty->fetch(__DIR__ . '/../tpl/overview.tpl');
    }

    public function getMuster($doc)
    {
        $doc = (int) $doc;
        if (DC()->get('doc') > 0) {
            $handle = md5('D3B!7C0NN3CT_' . date('Ymd') . 'AUTH_TOKEN');
            $soap = $this->API->mahnwesen();
            $res = $soap->getMusterDokument($handle, $doc);
            $download = "<a download='VOPRechnung-" . $doc . ".pdf' href='data:application/pdf;base64," . $res->file . "' title='Download pdf document' />Download PDF</a>";
            $embed = "$download<embed src='data:application/pdf;base64," . $res->file . "' width='100%' height='100%' alt='pdf' pluginspage='http://www.adobe.com/products/acrobat/readstep2.html' type='application/pdf'>";
        }

        return $embed;
    }

    public function fetchFancy($id)
    {
        switch ($id) {
            case 'getMuster':
            return $this->getMuster(DC()->get('doc'));
            break;
            case 'getBeleg':
            return $this->getBelegPDF(DC()->get('doc'));
            break;
            case 'overViewGateway':
            return $this->getOverViewGateway(DC()->get('pkCustomer'));
            break;
            case 'overView':
            return $this->getOverView(DC()->get('pkOrder'));
            break;
            case 'CSVData':
            return $this->HBCICSVFiles();
            break;
            case 'HBCIPayments':
            return $this->HBCIWriteBackPayments();
            break;
            case 'HBCIMatching':
            return $this->HBCIMatching();
            break;
            case 'sync':
            return $this->syncFancy();
            break;
            case 'hbcirequest':
            return $this->hbcirequestmanuell();
            break;
            case 'template':
            DC()->dataTypes->assignTemplateVars();

            return $this->pageUserSettings('template');
            break;
            case 'zahlungsabgleich':
            return $this->zahlungsabgleichManuell();
            break;
            case 'zasuche':
            return $this->zaSuche();
            break;
            case 'leadoc':
            return $this->getLEADoc();
            break;
            case 'vorschau':
            return $this->sendZahlungserinnerung((int) DC()->get('order'), true);
            break;
            case 'logout':
            $tpl = 'LOGOUT';
            break;
            default:
            break;
        }
    }

    public function getCurrentPage()
    {
        if (DC()->get('switchTo') && !(DC()->hasvalue('fancy'))) {
            $this->current_page = DC()->get('switchTo');
        }
        if ($this->current_page == null) {
            $this->current_page = 'start';
        }
    }

    public function setUserLogin($userId)
    {
        $this->loginData['logged_in'] = true;
        $this->user = $userlogin; // @todo fix undefined $userlogin
        //$this->setSession();
    }

    public function checkLogin()
    {
        if (DC()->hasvalue('sessid') && DC()->get('usr')) {
            $userlogin = $this->dataTypes->getUserLogin('', '');
            if ($userlogin > 0) {
                $this->setUserLogin($userlogin);
            }
        }

        if (DC()->hasvalue('login')) {
            $userlogin = $this->dataTypes->getUserLogin(DC()->get('userlogin'), DC()->get('passwd'));
            if ($userlogin > 0) {
                $this->loginData['logged_in'] = true;
                $this->user = $userlogin;
            }
        }

        if ($this->loginData != null && $this->loginData['logged_in'] && $this->user > 0) {
            $this->getSettings();

            $this->getCurrentPage();

            return true;
        }

        return false;
    }

    public function getConf($art, $standard, $global = false)
    {
        $shop = $global ? 0 : DC()->settings->selectedShop;

        $res = DC()->db->singleResult('SELECT datavalue from dc_meta where shopID = ' . (int) $shop . " and art = '" . DC()->db->dbEscape($art) . "'");

        if ($res == null) {
            $ins = new stdClass();
            $ins->shopID = $shop;
            $ins->art = ($art);
            $ins->datavalue = $standard;
            DC()->db->dbInsert('dc_meta', $ins, false);
            $res = ['datavalue' => $standard];
        }

        return $res['datavalue'];
    }

    public function setConf($art, $value, $global = false)
    {
        $shop = $global ? 0 : DC()->settings->selectedShop;
        $obj = new stdClass();
        $obj->datavalue = ($value);
        if (DC()->db->dbUpdate('dc_meta', $obj, 'shopID = ' . (int) $shop . " and art = '" . DC()->db->dbEscape($art) . "'")) {
            $this->View('SUCCESS_MSG', 'Die Konfiguration wurde gespeichert');

            return true;
        }

        return false;
    }

    public function findkeyvalue($array, $key, $val)
    {
        foreach ($array as $item) {
            if (is_array($item) && find_key_value($item, $key, $val)) {
                return true;
            }
            if (isset($item[$key]) && $item[$key] == $val) {
                return true;
            }
        }

        return false;
    }

    public function flush_session()
    {
        Shopware()->BackendSession()->{$this->sessName} = null;
    }

    public function setSession()
    {
        $this->db->dbClose();

        $this->db = null;
        $this->smarty = null;
        $this->openConnection = null;
        $this->dataTypes = null;

        $this->hbci->bestellungen = [];

        Shopware()->BackendSession()->{$this->sessName} = serialize($this);
    }

    public static function encrypt($string, $key = ';vOp!deB1TC0nn3CTSEnCKey.:')
    {
        $result = '';
        for ($i = 0, $iMax = strlen($string); $i < $iMax; ++$i) {
            $char = substr($string, $i, 1);
            $keychar = $key[($i % strlen($key)) - 1];
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }

    public static function decrypt($string, $key = ';vOp!deB1TC0nn3CTSEnCKey.:')
    {
        $result = '';
        $string = base64_decode($string);

        for ($i = 0, $iMax = strlen($string); $i < $iMax; ++$i) {
            $char = $string[$i];
            $keychar = $key[($i % strlen($key)) - 1];
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }

    public function doInstallOrUpdate($lastVersion)
    {
        $_lastVersion = $lastVersion;
        $lastVersion = (int) str_replace('.', '', $lastVersion);
        $installdir = __DIR__ . '/../_install/';
        $installed = true;
        $files = scandir($installdir);
        $sql = [];
        $versions = [];

        foreach ($files as $file) {
            if (strlen($file) > 2) {
                $versions[] = str_replace('.sql', '', $file);
            }
        }

        usort($versions, 'version_compare');

        foreach ($versions as $file) {
            if (strlen($file) > 2) {
                $file .= '.sql';
                $sqlVersion = str_replace('.sql', '', $file);
                if (version_compare($sqlVersion, $_lastVersion, '>')) {
                    $query = file_get_contents($installdir . $file);
                    try {
                        $queryRows = explode(';', $query);

                        foreach ($queryRows as $singlequery) {
                            if (strlen($singlequery) > 10 && $installed) {
                                if (!DC()->db->dbQuery($singlequery)) {
                                    $installed = false;
                                }
                            }
                        }
                        if ($installed) {
                            $this->getConf('dbVersion', 0, true);
                            $this->setConf('dbVersion', $sqlVersion, true);
                        }
                    } catch (Exception $e) {
                        return false;
                    }
                }
            }
        }
        if ($installed) {
            $this->getConf('dbVersion', 0, true);
            $this->setConf('dbVersion', self::$DC_VERSION, true);
        }

        return $installed;
    }

    public function getUpdate()
    {
        $dbVersion = $this->getDBVersion();

        if ($dbVersion == '0') {
            $this->View('installmode', 'install');
        } else {
            $this->View('installmode', 'update');
        }

        return $dbVersion != self::$DC_VERSION;
    }

    public function getDBVersion()
    {
        $dc_meta = DC()->db->tableExists('dc_meta');
        if (!$dc_meta) {
            $dbVersion = '0';
        } else {
            $dbVersion = $this->getConf('dbVersion', 0, true);
        }

        return $dbVersion;
    }

    public function checkInstallation()
    {
        $updateRequired = $this->getUpdate();
        $installstate = $updateRequired !== true;
        $dbVersion = $this->getDBVersion();
        if ($updateRequired) {
            if (DC()->hasvalue('cronjob')) {
                header('Content-Type: application/json');
                echo json_encode(['InstallationRequired' => true]);
            } else {
                $this->View('version', self::$DC_VERSION);
                try {
                    $soap = new SoapClient(self::$SOAP);
                    $handshake = $soap->handshake();
                    $handshake = $handshake->status;
                } catch (Exception $e) {
                }
                $this->View('handshake', $handshake);

                if (!DC()->hasvalue('install')) {
                    $this->View('SOFTWARELIZENZ', utf8_encode(file_get_contents('Softwarelizenzvertragsbedingungen.txt')));
                    echo  $this->smarty->fetch(__DIR__ . '/../tpl/install.tpl');
                } else if ($this->doInstallOrUpdate($dbVersion)) {
                    $this->View('SUCCESS_MSG', 'Installation abgeschlossen.');
                    $installstate = true;
                } else {
                    $this->View('INSTALL_ERROR', 'Installation fehlgeschlagen');
                    echo  $this->smarty->fetch(__DIR__ . '/../tpl/install.tpl');
                }
            }
        }

        return $installstate;
    }
}

class CoreInstance
{
    public static $getCore = null;
}
