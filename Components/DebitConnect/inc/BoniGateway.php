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

class DebitConnect_BoniGateway
{
    public $WSDL = 'https://api.eaponline.de/bonigateway.php?wsdl';
    public $userLogin;
    public $userPass;
    public $api;
    public $projecte_b2b;
    public $projecte_b2c;
    public $logged_in = false;
    public $kennziffern;

    public function Log($art, $txt, $errormsg)
    {
        DC()->log($art, $txt, $errormsg);
    }

    public function initApi()
    {
        $this->api = new SoapClient($this->WSDL, ['encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1]);
    }

    public function getRequest()
    {
        $ergebnis = [];
        $this->initApi();
        $requestParams = new stdClass();
        $requestParams->firstname = DC()->db->dbEscape(DC()->get('firstname'));
        $requestParams->lastname = DC()->db->dbEscape(DC()->get('lastname'));
        $requestParams->company = DC()->db->dbEscape(DC()->get('company'));
        $requestParams->salutation = DC()->db->dbEscape(DC()->get('salutation'));
        $requestParams->street = DC()->db->dbEscape(DC()->get('street'));
        $requestParams->city = DC()->db->dbEscape(DC()->get('city'));
        $requestParams->zipcode = DC()->db->dbEscape(DC()->get('zipcode'));
        $requestParams->country = DC()->db->dbEscape(DC()->get('country'));
        $requestParams->DateOfBirth = DC()->db->dbEscape(DC()->get('DateOfBirth'));

        $requestParams->country = DC()->db->dbEscape(DC()->get('country'));
        $kaskade_inhaber = DC()->get('kaskade_inhaber') == 'on' ? true : false;
        $requestParams->project = DC()->db->dbEscape(DC()->get('request_project'));

        try {
            if (DC()->get('request_art') == 'B2C') {
                $response = $this->api->getSCHUFAB2C($this->userLogin,$this->userPass,$requestParams->project,'shoplogin',$requestParams->firstname,$requestParams->lastname,
                                                    $requestParams->salutation,$requestParams->DateOfBirth,$requestParams->street,$requestParams->zipcode,$requestParams->city,
                                                    '', '', '', '0', 'FALSE', '', '', '', '', $requestParams->country);
                $ergebnis = ['ergebnis' => DC()->get('request_art'), 'response' => $response];
            } elseif (DC()->get('request_art') == 'Kaskade') {
                $response = $this->api->getSCHUFAB2B($this->userLogin,$this->userPass,$requestParams->project,'shoplogin',$requestParams->company,'',
                                                    $requestParams->street, $requestParams->zipcode, $requestParams->city, $requestParams->country, '', '', '0');

                $ergebnis = ['ergebnis' => DC()->get('request_art'), 'response' => $response];
            } elseif (DC()->get('request_art') == 'Kompakt') {
                if (DC()->get('request_id')) {
                    $type = 1;
                    $interesse = DC()->get('intresse');
                    $request_id = DC()->get('request_id');
                    $response = $this->api->getSCHUFAB2BVK($this->userLogin, $this->userPass, (int) DC()->get('kennziffer'), (int) $type, $request_id, $requestParams->company, $requestParams->street, $requestParams->zipcode, $requestParams->city, $interesse);
                    $ergebnis = ['ergebnis' => DC()->get('request_art'), 'response' => $response];
                } else {
                    $response = $this->api->getSCHUFAB2BTrefferliste($this->userLogin, $this->userPass, (int) DC()->get('kennziffer'), $requestParams->company, '', $requestParams->street, $requestParams->zipcode, $requestParams->city);
                    $ergebnis = ['trefferliste' => true, 'response' => $response];
                }
            } elseif (DC()->get('request_art') == 'Vollauskunft') {
                if (DC()->get('request_id')) {
                    $type = 0;
                    $interesse = DC()->get('intresse');
                    $request_id = DC()->get('request_id');
                    $response = $this->api->getSCHUFAB2BVK($this->userLogin, $this->userPass, (int) DC()->get('kennziffer'), (int) $type, $request_id, $requestParams->company, $requestParams->street, $requestParams->zipcode, $requestParams->city, $interesse);
                    $ergebnis = ['ergebnis' => DC()->get('request_art'), 'response' => $response];
                } else {
                    $response = $this->api->getSCHUFAB2BTrefferliste($this->userLogin, $this->userPass, (int) DC()->get('kennziffer'), $requestParams->company, '', $requestParams->street, $requestParams->zipcode, $requestParams->city);
                    $ergebnis = ['trefferliste' => true, 'response' => $response];
                }
            }
        } catch (Exception $e) {
            DC()->smarty->assign('gateway_exception', 'Temp. Störung der Schnittstelle');
        }

        if (array_key_exists('ergebnis', $ergebnis)) {
            $this->createLog(DC()->get('request_art'), $response, $e, $requestParams);
        }

        return $ergebnis;
    }

    public function getResponseCode($responseData)
    {
        if (!$responseData->responseCode) {
            return '';
        }

        $codes = [0 => '',
                            20 => 'SCHUFA B2C',
                           21 => 'B2C Offlinesuche',
                           17 => 'Kreditlimit',
                           22 => 'Blackliste',
                           80 => 'Ungeprüft',
                           30 => 'SCHUFA B2B',
                           31 => 'B2B Offlinesuche',
                           10000 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
                           10001 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
                           10002 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
                           10003 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
                           10004 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
                           10005 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
                           10006 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>", ];

        return $codes[$responseData->responseCode];
    }

    public function createLog($art, $response, $exception = null, $requestParams)
    {
        try {
            $pkCustomer = DC()->get('pkCustomer');
            $eintrag = new stdClass();
            $eintrag->cArt = DC()->db->dbEscape('Manuelle Prüfung');
            $eintrag->sessToken = DC()->db->dbEscape(md5('manuell' . $pkCustomer));
            $eintrag->tstamp = DC()->db->dbEscape(date('d.m.Y H:i:s'));
            $eintrag->warenkorb = '0';
            $eintrag->zahlungsart = 'Manuell';
            $eintrag->pruefung = true;
            $eintrag->customer_vname = $requestParams->firstname;
            $eintrag->customer_nname = $requestParams->lastname;
            $eintrag->customer_firma = $requestParams->company;
            $eintrag->ergebnis = DC()->db->dbEscape(@$response->secure_payment);
            $eintrag->error = DC()->db->dbEscape(@$response->error);
            $eintrag->responseCode = @$response->responseCode > 0 ? (int) @$response->responseCode : 0;
            $eintrag->responseText = DC()->db->dbEscape(@$this->getResponseCode($response->responseData));
            $eintrag->pkCustomer = (int) $pkCustomer > 0 ? (int) $pkCustomer : 0;
            $eintrag->scoreInfo = DC()->db->dbEscape(@$response->Scorebereich . '-' . @$response->Scorewert);
            $eintrag->bPDF = isset($response->bPDF) ? $response->bPDF : null;
            DC()->db->dbInsert('dc_gatewaylog', $eintrag, false);
        } catch (Exception $e) {
            $eintrag->scoreInfo = '';
        }
        if ($exception != null) {
            $eintrag->error = $this->dbEscape($exception);
        }
    }

    public function checkLoginGateway($username, $pass)
    {
        $this->initApi();
        $pass = md5($pass);
        $this->projecte_b2b = [];
        $this->projecte_b2c = [];

        try {
            $wsresponse = $this->api->getGatewayLogin($username, $pass);

            if ($wsresponse->status == 'True') {
                $this->logged_in = true;
                $this->userLogin = $username;
                $this->userPass = $pass;
                $projectresponse = $this->api->getProject($this->userLogin, $this->userPass, '1');
                foreach ($projectresponse as $project) {
                    if (!isset($project->Error)) {
                        if ($project->projecttype == 'B2C') {
                            $this->projecte_b2c[] = $project;
                        } elseif ($project->projecttype == 'B2B') {
                            $this->projecte_b2b[] = $project;
                        }
                    }
                }
                $this->kennziffern = $this->api->getSCHUFAKennziffern($this->userLogin, $this->userPass);

                return true;
            }
            DC()->smarty->assign('gateway_passwd_mismatch', true);
        } catch (Exception $e) {
            $this->Log('GatewayLogin', $e->getMessage(), 10);
            DC()->smarty->assign('gateway_exception', 'Temp. Störung der Schnittstelle');
        }

        return false;
    }
}
