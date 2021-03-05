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

class EAP_Functions
{
    public $checkout_session;
    public $requestID;
    public $global_session;
    public $JTL_MAILER;
    public $settingsArray;
    /** URL zur EAP-Schnittstelle **/
    private $url;

    var $shopId;

    public function __construct($checkout_session)
    {
        $this->checkout_session = $checkout_session;
    }

    public function ExceptionLogger($logArt, $exception, $settings, $lang)
    {
        /*
        try
        {
            if(strlen($settings["jtl_eap_error_mail_notice"])>0)
            {


            }
        }catch(Exception $e)
        {

        }

            */
    }

    public function dbEncode($value)
    {
        return utf8_encode($value);
    }

    public function dbEscape($value)
    {
        return $value;
        //return $this->dbEncode($value);
        /*
            return Shopware()->Db()->quote($value);
        */
    }

    public function dbQuery($query)
    {
        return Shopware()->Db()->fetchall($query);
    }

    public function dbUpdate($table, $pkcolumn, $pkvalue, $values)
    {
        // @todo JTL_VERSION undeclared
        return version_compare(JTL_VERSION, 400, '>=') == false ? $GLOBALS['DB']->updateRow($table, $pkcolumn, $pkvalue, $values) : Shop::DB()->update($table, $pkcolumn, $pkvalue, $values);
    }

    public function dbInsert($table, $object)
    {
        return Shopware()->Db()->insert($table, (array) $object);
    }

    public function createSettingsArray($input)
    {
        $return = [];
        foreach ($input as $setting) {
            $return[$setting->cName] = $setting->cWert;
        }

        return $return;
    }

    public function getEmailConfiguration()
    {
        return $this->dbQuery('SELECT cWert FROM teinstellungen WHERE cName = "email_master_absender"', 1);
    }

    public function updatewhiteListEntry($kKunde, $remove)
    {
        if ($remove) {
            return $this->dbQuery('delete from xplugin_jtl_eap_whitelist where kKunde = ' . (int) $kKunde, 1);
        }

        $newins = new stdClass();
        $newins->kKunde = (int) $kKunde;
        $newins->nBoni = (int) 1;
        $newins->nIdent = (int) 1;

        return $this->dbInsert('xplugin_jtl_eap_whitelist', $newins);
    }

    public function checkBoniWhiteListEntry($kKunde)
    {
        $isWhitelisted = $this->dbQuery('select count(kKunde) as checkwhitelist from xplugin_jtl_eap_whitelist where kKunde = ' . (int) $kKunde, 1);

        return $isWhitelisted->checkwhitelist > 0 ? true : false;
    }

    public function getDecrypted($val)
    {
        return trim(entschluesselXTEA($val));
    }

    public function getEncrypted($val)
    {
        return verschluesselXTEA(trim($val));
    }

    public function lockedPaymentsArray()
    {
        $oZahlungsartRegeln_arr = $this->dbQuery('SELECT * FROM xplugin_jtl_eap_zahlungsarten
                                                                JOIN tzahlungsart ON tzahlungsart.kZahlungsart=xplugin_jtl_eap_zahlungsarten.kZahlungsart', 2);

        return $oZahlungsartRegeln_arr;
    }

    public function neueVersandRegel($del, $insert)
    {
        if ($del > 0) {
            $bOk = $this->dbQuery('DELETE FROM xplugin_jtl_eap_versandarten where kVersandart = ' . (int) $del, 3);
        }

        if ($insert > 0) {
            $oGruppeNeu = new stdClass();
            $oGruppeNeu->kVersandart = (int) $insert;
            $bOk = $this->dbInsert('xplugin_jtl_eap_versandarten', $oGruppeNeu);
        }
    }

    public function getShippingMethods()
    {
        $versandarten = $this->dbQuery('SELECT cName,tversandart.kVersandart,xplugin_jtl_eap_versandarten.kVersandart as checkValue FROM `tversandart` left outer join xplugin_jtl_eap_versandarten on xplugin_jtl_eap_versandarten.kVersandart = tversandart.kVersandart', 2);

        return $versandarten;
    }

    public function lockedShipping()
    {
        $oZahlungsartRegeln_arr = $this->dbQuery('SELECT kVersandart FROM xplugin_jtl_eap_versandarten ', 2);

        return $oZahlungsartRegeln_arr;
    }

    public function getAllActivePaymentsArray($versand)
    {
        $oZahlungsartRegeln_arr = $this->dbQuery('SELECT tzahlungsart.* FROM `tversandartzahlungsart` 
                                                    left join tzahlungsart on tversandartzahlungsart.kzahlungsart = tzahlungsart.kzahlungsart where tversandartzahlungsart.kversandart = ' . (int) $versand->kVersandart, 2);

        return $oZahlungsartRegeln_arr;
    }

    public function checkKundengruppeAll($kKundengruppe, $settings)
    {
        return in_array($kKundengruppe, $settings);
    }

    public function checkKundengruppeBoni($kKundengruppe)
    {
        $oKundengruppe = $this->dbQuery('SELECT * FROM xplugin_jtl_eap_kundengruppen
                                                        WHERE nBoni = 1 and kKundengruppe=' . (int) $kKundengruppe, 1);

        return (bool) $oKundengruppe;
    }

    public function checkkundenGruppeIdent($kKundengruppe)
    {
        $oKundengruppe = $this->dbQuery('SELECT * FROM xplugin_jtl_eap_kundengruppen
                                                        WHERE nIdent = 1 and kKundengruppe=' . (int) $kKundengruppe, 1);

        return (bool) $oKundengruppe;
    }

    public function getParsedDate($inputdata)
    {
        if (strlen($inputdata) < 10 or $inputdata == '00-00-0000' or $inputdata == '0000-00-00') {
            // KEIN GEBURTSDATUM VORHANDEN
            return '00.00.0000';
        }

        try {
            $date = new DateTime($inputdata);
            $geb = $date->format('d.m.Y');
        } catch (Exception $e) {
            $geb = '00.00.0000';
        }

        return $geb;
    }

    public function replaceHTML($inputtext, $replaceUnd = true)
    {
        $inputtext = utf8_decode($inputtext);
        $replacements = [
            '&' => '',
            chr(192) => 'Ae',
            chr(193) => 'Ae',
            chr(194) => 'Ae',
            chr(195) => 'Ae',
            chr(197) => 'Ae',
            chr(198) => 'Ae',
            chr(199) => 'C',
            chr(200) => 'E',
            chr(201) => 'E',
            chr(202) => 'E',
            chr(203) => 'E',
            chr(204) => 'I',
            chr(205) => 'I',
            chr(206) => 'I',
            chr(207) => 'I',
            chr(208) => 'D',
            chr(209) => 'N',
            chr(210) => 'Oe',
            chr(211) => 'Oe',
            chr(212) => 'Oe',
            chr(213) => 'Oe',
            chr(217) => 'Ue',
            chr(218) => 'Ue',
            chr(219) => 'Ue',
            chr(221) => 'Y',
            chr(224) => 'ae',
            chr(225) => 'ae',
            chr(226) => 'ae',
            chr(227) => 'ae',
            chr(229) => 'ae',
            chr(230) => 'ae',
            chr(231) => 'c',
            chr(232) => 'e',
            chr(233) => 'e',
            chr(234) => 'e',
            chr(235) => 'e',
            chr(236) => 'i',
            chr(237) => 'i',
            chr(238) => 'i',
            chr(239) => 'i',
            chr(240) => 'oe',
            chr(241) => 'n',
            chr(242) => 'oe',
            chr(243) => 'oe',
            chr(244) => 'oe',
            chr(245) => 'oe',
            chr(249) => 'ue',
            chr(250) => 'ue',
            chr(251) => 'ue',
            chr(253) => 'y',
            chr(255) => 'y'
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $inputtext);
    }

    public function istGesperrt($kZahlungsart, $settingsPayments)
    {

        return in_array($kZahlungsart, $settingsPayments);
    }

    public function setzeKundengruppen($kgruppe, $insert)
    {
        if (!$insert) {
            $bOk = $this->dbQuery('DELETE FROM xplugin_jtl_eap_kundengruppen where kKundengruppe = ' . (int) $kgruppe, 3);
        } else {
            $oGruppeNeu = new stdClass();
            $oGruppeNeu->kKundengruppe = (int) $kgruppe;
            $bOk = $this->dbInsert('xplugin_jtl_eap_kundengruppen', $oGruppeNeu);
        }

        return 0;
    }

    public function updateRegel($art, $kundengruppe, $value)
    {
        $regel = new stdClass();
        $regel->kKundengruppe = (int) $kundengruppe;
        if ($art == 'boni') {
            $regel->nBoni = (int) $value;
        } elseif ($art == 'ident') {
            $regel->nIdent = (int) $value;
        } elseif ($art == 'move') {
            $regel->nIdentMove = (int) $value;
            if ($value == '1') {
                $this->dbQuery('update xplugin_jtl_eap_kundengruppen set nIdentMove = 0', 1);
            }
        }

        $bOk = $this->dbUpdate('xplugin_jtl_eap_kundengruppen', 'kKundengruppe', $kundengruppe, $regel);
    }

    public function createKundenGruppenEntry()
    {
        $kundengruppe = $this->leseKundengruppen();
        foreach ($kundengruppe as $gruppe) {
            if ($gruppe->checkValue == null) {
                $regel = new stdClass();
                $regel->nBoni = (int) 0;
                $regel->nIdent = (int) 0;

                $regel->kKundengruppe = (int) $gruppe->kGruppe;
                $bOk = $this->dbInsert('xplugin_jtl_eap_kundengruppen', $regel);
            }
        }
    }

    public function leseKundengruppen($kKundengruppe = 0)
    {
        if ($kKundengruppe == 0) {
            $oKundengruppen_arr = $this->dbQuery('SELECT xplugin_jtl_eap_kundengruppen.kKundengruppe as checkValue, tkundengruppe.cName,tkundengruppe.cName,tkundengruppe.kKundengruppe as kGruppe,IFNULL(xplugin_jtl_eap_kundengruppen.nBoni,0) as nBoni ,IFNULL(xplugin_jtl_eap_kundengruppen.nIdent,0) as nIdent,IFNULL(xplugin_jtl_eap_kundengruppen.nIdentMove,0) as nIdentMove FROM xplugin_jtl_eap_kundengruppen
                                                                RIGHT OUTER JOIN tkundengruppe ON tkundengruppe.kKundengruppe=xplugin_jtl_eap_kundengruppen.kKundengruppe
                                                                ORDER by tkundengruppe.kKundengruppe', 2);
        } else {
            $oKundengruppen_arr = $this->dbQuery('SELECT xplugin_jtl_eap_kundengruppen.*, tkundengruppe.cName FROM xplugin_jtl_eap_kundengruppen
                                                                JOIN tkundengruppe ON tkundengruppe.kKundengruppe=xplugin_jtl_eap_kundengruppen.kKundengruppe
                                                                WHERE xplugin_jtl_eap_kundengruppen.kKundengruppe=' . (int) $kKundengruppe . '
                                                                ORDER by kKundengruppe', 2);
        }

        return $oKundengruppen_arr;
    }

    public function clearDataRechnungsadresse($rechnungsadresse)
    {
        $inputdata = $rechnungsadresse;

        if (strlen(@$inputdata->dGeburtstag) < 10 or $inputdata->dGeburtstag == '00-00-0000') {
            $geb = '00.00.0000'; // KEIN GEBURTSDATUM VORHANDEN
        } else {
            try {
                $date = new DateTime($inputdata->dGeburtstag);
                $geb = $date->format('d.m.Y');
            } catch (Exception $e) {
                $geb = '00.00.0000';
            }
        }

        $output = ['Vorname' => $this->replaceHTML($inputdata->cVorname),
            'Nachname' => $this->replaceHTML($inputdata->cNachname),
            'Firma' => $this->replaceHTML($inputdata->cFirma, false),
            'PLZ' => $inputdata->cPLZ,
            'Ort' => $this->replaceHTML($inputdata->cOrt),
            'Strasse' => $this->replaceHTML(($inputdata->cStrasse) . ' ' . $inputdata->cHausnummer),
            'nr' => $inputdata->cHausnummer,
            'land' => $inputdata->cLand,
            'geschlecht' => ($inputdata->cAnrede == 'm') ? 1 : 2,
            'geb' => $geb,
            'mail' => $this->replaceHTML($inputdata->cMail),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'tel' => $inputdata->cTel, ];

        foreach ($output as $key => $val) {
            $output[$key] = utf8_encode($output[$key]);
        }

        return $output;
    }

    public function clearDataLieferadresse($lieferadresse)
    {
        $inputdata = $lieferadresse;

        $output = ['Vorname' => $this->replaceHTML($inputdata->cVorname),
            'Nachname' => $this->replaceHTML($inputdata->cNachname),
            'Firma' => $this->replaceHTML($inputdata->cFirma, false),
            'PLZ' => $inputdata->cPLZ,
            'Ort' => $this->replaceHTML($inputdata->cOrt),
            'Strasse' => $this->replaceHTML($inputdata->cStrasse . ' ' . $inputdata->cHausnummer),
            'nr' => $inputdata->cHausnummer,
            'land' => $inputdata->cLand, ];

        foreach ($output as $key => $value) {
            $output[$key] = utf8_encode($value);
        }

        return $output;
    }

    public function getZahlungsartRegel($kZahlungsart)
    {
        if ((int) $kZahlungsart) {
            $oZahlungsartRegel = $this->dbQuery('SELECT * FROM xplugin_jtl_eap_zahlungsarten
                                                                WHERE kZahlungsart=' . (int) $kZahlungsart, 1);

            if ($oZahlungsartRegel) {
                return $oZahlungsartRegel;
            }
        }

        return false;
    }

    public function writeBackPayment($requestParams, $id, $payment, $settings, $ordernumber)
    {
        try {
            if ($id > 0) {
                $securedPayment = $this->istGesperrt($requestParams['Zahlungsart']->kZahlungsart, $settings['boniPayments']) ? 1 : 0;
                $var = new stdClass();

                $var->abschluss = $this->dbEscape($requestParams['Zahlungsart']->cName);
                Shopware()->Db()->executeUpdate('UPDATE dc_gatewaylog SET abschluss = :abschluss WHERE sessToken = :sessToken',
                    [
                        ':abschluss' => $var->abschluss,
                        ':sessToken' => $this->dbEscape(md5($this->checkout_session . session_id())),
                    ]);

                $client = new SoapClient('https://api.eaponline.de/bonigateway.php?wsdl', ['trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE]);
                $result = $client->writebackpayment($settings['jtl_eap_userid'], md5($settings['jtl_eap_passwort']), $id, $payment, $ordernumber, $securedPayment);

                return $result->verified;
            }
        } catch (Exception $e) {
            return false;
        }
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
            23 => 'Whitelist',
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

    public function verifiedAndMovedToKundengruppe($type, $nType = 1)
    {
        if ($type->responseData->verified == true && $type->requestParams['Rechnungsadresse']->kKunde > 0) {
            if (($this->settingsArray['jtl_eap_ident_moveto']) !== 'dontmove') {
                //Shopware()->Session()->sUserGroup = $this->settingsArray['jtl_eap_ident_moveto'];
                $identchecklog = new stdClass();
                $identchecklog->kKunde = (int) $type->requestParams['Rechnungsadresse']->kKunde;
                $identchecklog->handle = $this->dbEscape($type->requestParams['currenthandle']);
                $identchecklog->tstamp = $this->dbEscape(date('Y-m-d H:i:s'));
                $identchecklog->type = (int) $nType;
                $this->dbInsert('dc_identcheck_log', $identchecklog);

                Shopware()->Db()->executeUpdate(' UPDATE s_user SET customergroup = :customergroup WHERE id = :userId',
                    [
                        ':userId' => Shopware()->Session()->sUserId,
                        ':customergroup' => $this->settingsArray['jtl_eap_ident_moveto'],
                    ]);
            }
        }
    }

    public function createLog($type, $exception = null)
    {
        try {
            $eintrag = new stdClass();
            $eintrag->cArt = $this->dbEscape(($type->LOG_NAME));
            $eintrag->sessToken = $this->dbEscape(md5($this->checkout_session . session_id()));
            $eintrag->tstamp = $this->dbEscape(date('d.m.Y H:i:s'));
            $eintrag->customer_vname = $this->dbEscape($type->requestParams['Rechnungsadresse']->cVorname);
            $eintrag->customer_nname = $this->dbEscape($type->requestParams['Rechnungsadresse']->cNachname);
            $eintrag->customer_firma = $this->dbEscape($type->requestParams['Rechnungsadresse']->cFirma);
            $eintrag->warenkorb = $type->requestParams['Warenkorb'];
            $eintrag->zahlungsart = $this->dbEscape($type->requestParams['Zahlungsart']->cName);
            $eintrag->pruefung = $this->dbEscape(@$type->requested);
            $eintrag->ergebnis = $this->dbEscape(@$type->responseData->secure_payment);
            $eintrag->error = $this->dbEscape(@$type->responseData->error);
            $eintrag->responseCode = @$type->responseData->responseCode > 0 ? (int) @$type->responseData->responseCode : 0;
            $eintrag->responseText = $this->dbEscape(@$this->getResponseCode($type->responseData));
            $eintrag->pkCustomer = (int) $type->requestParams['Rechnungsadresse']->kKunde;
            $eintrag->scoreInfo = $this->dbEscape(@$type->responseData->Scorebereich . '-' . @$type->responseData->Scorewert);
        } catch (Exception $e) {
            $eintrag->scoreInfo = '';
        }
        if ($exception != null) {
            $eintrag->error = $this->dbEscape($exception);
        }
        $this->schreibeEAPLog($eintrag);
    }

    public function schreibeEAPLog($oLogEintrag)
    {
        return $this->dbInsert('dc_gatewaylog', $oLogEintrag);
    }

    public function neueRegel($kZahlungsart, $nScorewert)
    {
        $oEAPRegel = $this->dbQuery('SELECT * FROM xplugin_jtl_eap_zahlungsarten
                                                        WHERE kZahlungsart=' . (int) $kZahlungsart, 1);
        $oRegelNeu = new stdClass();
        if (!$oEAPRegel) {
            // Regel anlegen

            $oRegelNeu->nMaxScore = (int) $nScorewert;
            $oRegelNeu->kZahlungsart = (int) $kZahlungsart;
            $bOk = $this->dbInsert('xplugin_jtl_eap_zahlungsarten', $oRegelNeu);

            if ($bOk) {
                return 0;
            }
        } else {
            // Regel ändern
            $oRegelNeu->nMaxScore = (int) $nScorewert;
            $bOk = $this->dbUpdate('xplugin_jtl_eap_zahlungsarten', 'kZahlungsart', (int) $kZahlungsart, $oRegelNeu);
            if ($bOk) {
                return 0;
            }
        }

        return 5;
    }

    public function entferneRegel($kZahlungsart)
    {
        return $this->dbQuery('DELETE FROM xplugin_jtl_eap_zahlungsarten WHERE kZahlungsart=' . (int) $kZahlungsart, 3);
    }

    public function zeigeEAPLog($nEintraege = 30)
    {
        return $this->dbQuery('SELECT * FROM `xplugin_jtl_eap_fulllog` order by sessToken,tstamp desc LIMIT ' . (int) $nEintraege, 2);
    }
}

class EAPPruefung extends EAP_Functions
{
}
