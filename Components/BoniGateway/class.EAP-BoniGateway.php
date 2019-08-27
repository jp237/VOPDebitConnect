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

    include_once 'class.JTL-Shop.EAP.php';
    include_once 'class.EAP-Bonitaetspruefung.php';
    include_once 'class.EAP-IdentCheck.php';
class EAPBoniGateway
{
    public $wsdlURL = 'https://api.eaponline.de/bonigateway.php?wsdl';
    public $pluginSettings;
    public $settingsArray;
    public $requestParams;

    /** @var eap_postdirekt */
    public $postdirekt;
    /** @var EAP_Bonitaetspruefung */
    public $schufaBoni;
    /** @var EAP_IdentCheck */
    public $schufaIdent;

    // * PRÜFUNGSAKTIONEN....

    // * PRÜFUNGSAKTIONEN....
    public $smarty;
    /** @var EAP_Functions */
    public $functions;
    public $versandArt;
    public $requested;
    public $sprachArr;
    public $checkout_session;
    public $changed_card = 0;
    public $current_card = 0;
    // SHOPWARE ARGUMENTS

    public $shopware;
    public $request;
    public $response;
    public $controller;
    public $action;
    public $target;
    public $targetAction;
    public $view;
    public $userId;

    public function __construct()
    {
        if ($this->checkout_session == null) {
            $this->checkout_session = md5(date('Ymd') . rand(1000, 99999) . 'VOP');
        }
        $this->functions = new EAP_Functions($this->checkout_session);
    }

    public function getCurrentPluginConfig($settings, $smarty, $arguments)
    {
        $this->smarty = $smarty;

        // ** GET SHOPWARE ARGUMENTS ** //
        $this->shopware = new stdClass();
        $this->shopware->request = $arguments->getSubject()->Request();

        $this->shopware->response = $arguments->getSubject()->Response();
        $this->shopware->controller = $this->shopware->request->getControllerName();
        $this->shopware->action = $arguments->getRequest()->getActionName();
        $this->shopware->target = $this->shopware->request->getParam('sTarget');
        $this->shopware->targetAction = $this->shopware->request->getParam('sTargetAction');
        $this->smarty = $arguments->getSubject()->View();
        $this->shopware->userId = Shopware()->Session()->sUserId;
        // ** GET SHOPWARE ARGUMENTS ** //

        $this->settingsArray = $this->getCurrentSettingsArray();

        $this->pq_alert = $this->settingsArray['jtl_eap_selector_alert'];
        $this->pq_footer = $this->settingsArray['jtl_eap_selector_footer'];
        $this->pq_zahlung = $this->settingsArray['jtl_eap_selector_zahlung'];
        $this->pq_warenkorb = $this->settingsArray['jtl_eap_selector_warenkorb'];
        $this->pq_confirm = $this->settingsArray['jtl_eap_selector_confirm'];
        $this->sprachArr = $this->getBoniGatewayLanguage();
    }

    public function getBoniGatewayLanguage()
    {
        $shop = Shopware()->Shop()->getMain() !== null ? Shopware()->Shop()->getMain() : Shopware()->Shop();
        $selectedShop = $shop->getId();
        $settingslang = [];
        $lang = [];
        $entrys = Shopware()->Db()->fetchall('SELECT art,datavalue,comment from dc_gatewaymeta where nType = 1 and  shopID = ' . $selectedShop);

        foreach ($entrys as $entry) {
            $settingslang[$entry['art']]['value'] = $entry['datavalue'];
        }

        $stdentrys = Shopware()->Db()->fetchall('SELECT art,datavalue,comment from dc_gatewaymeta where nType = 1 and  shopID = 0');
        foreach ($stdentrys as $entry) {
            $lang[$entry['art']] = isset($settingslang[$entry['art']]) ? $settingslang[$entry['art']]['value'] : $entry['datavalue'];
        }

        $this->CheckRequiredFiles();

        return $lang;
    }

    public function getCurrentSettingsArray()
    {
        $settings = [];
        $shop = Shopware()->Shop()->getMain() !== null ? Shopware()->Shop()->getMain() : Shopware()->Shop();
        $selectedShop = $shop->getId();
        $entrys = Shopware()->Db()->fetchall('SELECT art,datavalue from dc_gatewaymeta where nType = 0 and shopID = ' . $selectedShop);
        foreach ($entrys as $entry) {
            $settings[$entry['art']] = is_object(json_decode($entry['datavalue'])) ? json_decode($entry['datavalue'], true) : $entry['datavalue'];
        }
        // CONVERT SETTINGS FROM DEBITCONNECT BACKEND TO JTL STRUCT
        // GRUNDEINSTELLUNGEN

        $convertedSettings['jtl_eap_userid'] = isset($settings['username']) ? $settings['username'] : null;
        $convertedSettings['jtl_eap_passwort'] = isset($settings['passwd']) ? $settings['passwd'] : null;
        $convertedSettings['jtl_eap_shopart'] = 0; // BETTER HANDLING IN SHOPWARE ;-)
        $convertedSettings['jtl_eap_error_mail_notice'] = isset($settings['logmail']) ? $settings['logmail'] : '';
        // BONITÄTSPRÜFUNG
        $convertedSettings['jtl_eap_abweichend'] = isset($settings['deviant']) ? $settings['deviant'] : 0;
        $convertedSettings['jtl_eap_exception_boni_handling'] = isset($settings['exceptionhandle']) ? $settings['exceptionhandle'] : 0;
        $convertedSettings['jtl_eap_ausland'] = isset($settings['request_nonde']) ? $settings['request_nonde'] : 0;
        $convertedSettings['jtl_eap_b2c'] = isset($settings['request_b2c']) ? $settings['request_b2c'] : 0;
        $convertedSettings['jtl_eap_b2b'] = isset($settings['request_b2b']) ? $settings['request_b2b'] : 0;
        $convertedSettings['jtl_eap_cardprotection'] = isset($settings['cardprotection']) ? $settings['cardprotection'] : 0;
        $convertedSettings['b2c_birthday'] = isset($settings['b2c_birthday']) ? $settings['b2c_birthday'] : 0;
        //IDENTCHECK
        $convertedSettings['jtl_eap_identcheck_use'] = isset($settings['ident']) ? $settings['ident'] : 0;
        $convertedSettings['jtl_eap_identcheck_use_art'] = isset($settings['ident_art']) ? $settings['ident_art'] : 0;
        $convertedSettings['jtl_eap_identcheck_qbit_output'] = 0; // NOT NEEDED;
        $convertedSettings['jtl_eap_attributname'] = isset($settings['ident_attribute']) ? $settings['ident_attribute'] : 0;
        $convertedSettings['jtl_eap_ident_recheck'] = isset($settings['ident_recheck_address']) ? $settings['ident_recheck_address'] : 0;
        $convertedSettings['jtl_eap_ident_moveto'] = isset($settings['ident_moveto']) ? $settings['ident_moveto'] : '';
        // DARSTELLUNGSOPTIONEN - MISSING WILL BE IMPLEMENTED !!
        $convertedSettings['boniPayments'] = [];
        foreach ($settings['boni_payments'] as $key => $value) {
            if ($value > 0) {
                $convertedSettings['boniPayments'][] = $key;
            }
        }
        $convertedSettings['boniCustomergroup'] = [];
        foreach ($settings['boni_customergroup'] as $key => $value) {
            if ($value > 0) {
                $convertedSettings['boniCustomergroup'][] = $key;
            }
        }
        $convertedSettings['identShipping'] = [];
        foreach ($settings['ident_shipping'] as $key => $value) {
            if ($value > 0) {
                $convertedSettings['identShipping'][] = $key;
            }
        }
        $convertedSettings['identCustomergroup'] = [];
        foreach ($settings['ident_customergroup'] as $key => $value) {
            if ($value > 0) {
                $convertedSettings['identCustomergroup'][] = $key;
            }
        }
        $this->smarty->assign('settings', $convertedSettings);

        $this->functions->settingsArray = $convertedSettings;

        return $convertedSettings;
    }

    public function CheckRequiredFiles()
    {
        $this->smarty->assign('btn_adresschange', $this->sprachArr['jtl_eap_identcheck_addrchange']);
        $this->smarty->assign('identcheck_notice', $this->sprachArr['jtl_eap_identcheck_notice']);

        $this->smarty->assign('tel', $this->settingsArray['jtl_eap_tel'] == 1 ? true : false);
        $this->smarty->assign('tel_text', $this->sprachArr['jtl_eap_tel_text']);
        $this->smarty->assign('abweichendeAdresse', $this->sprachArr['jtl_eap_abweichend_adresse']);
        $this->smarty->assign('geb', $this->settingsArray['jtl_eap_geb'] > 0 ? true : false);
        $this->smarty->assign('geb_text', $this->sprachArr['jtl_eap_geb_text']);
        $this->smarty->assign('btn_submit', $this->sprachArr['jtl_eap_continuebutton']);
        $this->smarty->assign('btn_close', $this->sprachArr['jtl_eap_abbrucbbutton']);
        $this->smarty->assign('eap_notice', $this->sprachArr['jtl_eap_eingabe_notice']);
        $this->smarty->assign('selector_zahlung', $this->settingsArray['jtl_eap_selector_zahlung']);

        $parsed_date = $this->functions->getParsedDate($this->requestParams['Rechnungsadresse']->dGeburtstag);
        $this->smarty->assign('dob_customer', $parsed_date == '00.00.0000' ? '' : $parsed_date);

        $this->smarty->assign('tel_customer', $this->requestParams['Rechnungsadresse']->cTel);
        $this->smarty->assign('IMGPATH', $this->pluginSettings->cFrontendPfadURLSSL);

        //$datepicker = "<script>"
    }

    public function generateFancyBoxContent()
    {
        $loadedTPL = "<div id='eap_fancyBox' style=\"width:auto;height:auto;display:none\">
								<div class='eap_h1'></div>
									" . $this->smarty->fetch($this->pluginSettings->cFrontendPfad . 'tpl/fancyBox.tpl') .
                          '</div>';
        //$loadedTPL = file_get_contents($this->pluginSettings->cFrontendPfad. "tpl/fancyBox.tpl");
        pq($this->pq_zahlung)->prepend("<input type='hidden' name='eap_hidden_geb' value=''>");
        pq($this->pq_zahlung)->prepend("<input type='hidden' name='eap_hidden_tel' value=''>");
        pq($this->pq_zahlung)->prepend("<input type='hidden' name='eap_hidden_company' value=''>");
        pq($this->pq_footer)->prepend($loadedTPL);

        return $loadedTPL;
    }

    public function getCurrentHandle($datenschutz = false)
    {
        try {
            $Kunde = $this->requestParams['Rechnungsadresse'];
            $basketAmount = $this->requestParams['Warenkorb'];
            $checkhash = md5($Kunde->cFirma . $Kunde->cVorname . $Kunde->cNachname . $Kunde->cStrasse . $Kunde->cHausnummer . $Kunde->cPLZ . $Kunde->cOrt . $Kunde->cMail);
        } catch (Exception $e) {
        }

        return $checkhash;
    }

    public function getCustomerGroupKey($groupkey)
    {
        $rs = Shopware()->Db()->fetchOne(
            'SELECT
                  id
                FROM
                  s_core_customergroups
                WHERE
                  groupkey = ?
                ',
            [$groupkey]
        );

        return $rs['id'];
    }

    public function updateBirthdayOnUserId($sUserId, $parsedBirthday)
    {
        try {
            $check_birthday = Shopware()->Db()->fetchOne('SELECT count(version) as checkval from s_schema_version where version = 730');
            if ($check_birthday > 0) {
                Shopware()->Db()->executeUpdate(' UPDATE s_user SET birthday = :birthday WHERE id = :userId',
                    [
                    ':userId' => Shopware()->Session()->sUserId,
                    ':birthday' => $parsedBirthday,
                    ]);
            } else {
                Shopware()->Db()->executeUpdate(' UPDATE s_user_billingaddress SET birthday = :birthday WHERE userID = :userId',
                    [
                    ':userId' => Shopware()->Session()->sUserId,
                    ':birthday' => $parsedBirthday,
                    ]);
            }
        } catch (Exception $e) {
        }
    }

    public function getCurrentRequestParams()
    {
        $customerData = Shopware()->Modules()->Admin()->sGetUserData();
        // CONVERT JTL OBJECT TO SHOPWARE OBJECT
        $rechnungsaddresse = new stdClass();
        $rechnungsaddresse->cVorname = $customerData['billingaddress']['firstname'];
        $rechnungsaddresse->cNachname = $customerData['billingaddress']['lastname'];
        $rechnungsaddresse->cFirma = $customerData['billingaddress']['company'];
        $rechnungsaddresse->cOrt = $customerData['billingaddress']['city'];
        $rechnungsaddresse->cPLZ = $customerData['billingaddress']['zipcode'];
        $rechnungsaddresse->cStrasse = $customerData['billingaddress']['street'];
        $rechnungsaddresse->cLand = $customerData['additional']['country']['countryiso'];

        $rechnungsaddresse->kKunde = $customerData['additional']['user']['id'];
        $rechnungsaddresse->kKundengruppe = $this->getCustomerGroupKey($customerData['additional']['user']['customergroup']);

        try {
            $check_birthday = Shopware()->Db()->fetchOne('SELECT count(version) as checkval from s_schema_version where version = 730');
            $birthdayDBValue = $check_birthday > 0 ? $customerData['additional']['user']['birthday'] : $customerData['billingaddress']['birthday'];
            $rechnungsaddresse->dGeburtstag = '00.00.0000';
            if ($birthdayDBValue) {
                // TRY TO PARSE TO D.M.Y

                $birthday_customer = new DateTime($birthdayDBValue);
                $rechnungsaddresse->dGeburtstag = $birthday_customer->format('d.m.Y');
                $this->smarty->assign('eap_bonigateway_birthday', $rechnungsaddresse->dGeburtstag);
            }
        } catch (Exception $e) {
            $rechnungsaddresse->dGeburtstag = '00.00.0000';
        }
        try {
            if ($this->shopware->request->getParam('eap_geburtstag')) {
                $parseBirthday = new DateTime($this->shopware->request->getParam('eap_geburtstag'));
                $this->updateBirthdayOnUserId($rechnungsaddresse->kKunde, $parseBirthday->format('Y-m-d'));
                $rechnungsaddresse->dGeburtstag = $parseBirthday->format('d.m.Y');
            }
        } catch (Exception $e) {
        }

        $rechnungsaddresse->cTel = $customerData['billingaddress']['phone'];
        $rechnungsaddresse->cMail = $customerData['additional']['user']['email'];
        $rechnungsaddresse->cAnrede = $customerData['billingaddress']['salutation'] === 'mr' ? 'm' : 'w';

        $lieferadresse = new stdClass();
        $lieferadresse->cVorname = $customerData['shippingaddress']['firstname'];
        $lieferadresse->cNachname = $customerData['shippingaddress']['lastname'];
        $lieferadresse->cFirma = $customerData['shippingaddress']['company'];
        $lieferadresse->cOrt = $customerData['shippingaddress']['city'];
        $lieferadresse->cPLZ = $customerData['shippingaddress']['zipcode'];
        $lieferadresse->cStrasse = $customerData['shippingaddress']['street'];

        $this->requestParams['Rechnungsadresse'] = $rechnungsaddresse;
        $this->requestParams['Lieferadresse'] = $lieferadresse;
        $basket = Shopware()->Modules()->Basket()->sGetBasket();

        $this->requestParams['Warenkorb'] = round($basket['AmountNumeric'], 0);
        $this->requestParams['art'] = strlen(($this->requestParams['Rechnungsadresse']->cFirma)) > 1 ? 'B2B' : 'B2C';
        $this->requestParams['check_firma'] = false;

        $this->requestParams['personAbweichend'] = false;
        $this->requestParams['adresseAbweichend'] = false;

        if ($this->requestParams['Rechnungsadresse']->cVorname != $this->requestParams['Lieferadresse']->cVorname || $this->requestParams['Rechnungsadresse']->cNachname != $this->requestParams['Lieferadresse']->cNachname) {
            $this->requestParams['personAbweichend'] = true;
            $this->requestParams['adresseAbweichend'] = true;
        } else {
            if ($this->requestParams['Rechnungsadresse']->cStrasse != $this->requestParams['Lieferadresse']->cStrasse || $this->requestParams['Rechnungsadresse']->cPLZ != $this->requestParams['Lieferadresse']->cPLZ || $this->requestParams['Rechnungsadresse']->cOrt != $this->requestParams['Lieferadresse']->cOrt || $this->requestParams['Rechnungsadresse']->cHausnummer != $this->requestParams['Lieferadresse']->cHausnummer) {
                $this->requestParams['adresseAbweichend'] = true;
            }
        }

        //$this->requestParams["Warenkorb"] = number_format($this->requestParams["Warenkorb"],0,"","");
        $this->requestParams['currenthandle'] = $this->getCurrentHandle(false);
        $sess = Shopware()->Session();
        if ($sess['sOrderVariables'] != null) {
            $sOrderVariables = $sess['sOrderVariables']->getArrayCopy();
            if ($sOrderVariables['sPayment']['id'] > 0) {
                $this->requestParams['Zahlungsart']->cName = $sOrderVariables['sPayment']['description'];
                $this->requestParams['Zahlungsart']->kZahlungsart = $sOrderVariables['sPayment']['id'];
            }

            if ($sOrderVariables['sDispatch']['id'] > 0) {
                $this->versandArt = $sOrderVariables['sDispatch']['id'];
            }
        }

        $this->assignBoniTemplateVars();
    }

    public function assignBoniTemplateVars()
    {
        $this->smarty->assign('secure_payments_bonigateway', $this->settingsArray['boniPayments']);
        $this->smarty->assign('headline_boni', $this->sprachArr['headline_boni']);
        $this->smarty->assign('jtl_eap_eingabe_notice', $this->sprachArr['jtl_eap_eingabe_notice']);
        $this->smarty->assign('eap_request_type', $this->requestParams['art']);
    }

    public function identCheckKundeAddressChange()
    {
        if ($this->settingsArray['jtl_eap_ident_recheck'] == 0) {
            return false;
        }

        if ($this->requireFullIDCard()) {
            // IDENTCHECK DARF NUR IDCARD 5 SEIN
            $query = "SELECT handle FROM dc_identcheck_log  where type = 2 and handle = '" . $this->functions->dbEscape($this->requestParams['currenthandle']) . "' and kKunde = " . (int) $this->requestParams['Rechnungsadresse']->kKunde;
        } else {
            // ALTERSCHECK IST IDCARD EGAL
            $query = "SELECT handle FROM dc_identcheck_log  where type < 3 and handle = '" . $this->functions->dbEscape($this->requestParams['currenthandle']) . "' and kKunde = " . (int) $this->requestParams['Rechnungsadresse']->kKunde;
        }
        $val = $this->functions->dbQuery($query, 1);
        if (@$val[0]['handle'] != @$this->requestParams['currenthandle']) {
            return true;
        }

        return false;
    }

    public function identCheckAlwaysOrAttribute()
    {
        if ($this->settingsArray['jtl_eap_identcheck_use_art'] == 0) {
            $this->setNoticeAgeCheck(false);

            return true;
        }

        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        foreach ($basket['content'] as $article) {
            $warenkorbpos = $article['additional_details'];

            for ($i = 1; $i <= 20; ++$i) {
                if (strtoupper($warenkorbpos['attr' . $i]) == strtoupper($this->settingsArray['jtl_eap_attributname'])) {
                    $this->setNoticeAgeCheck(false);

                    return true;
                }
            }
        }

        return false;
    }

    public function fetchIdentCheckTemplate($altersueberpruefung = true)
    {
        $this->smarty->assign('schufa_idcheck_required', $altersueberpruefung);
    }

    public function renamePaymentStepIdentCheck()
    {
    }

    /** @deprecated  */
    public function requireFullIDCard()
    {
        return false;
    }

    public function RemovePaymentWallSetIdentCheckWall()
    {
        $removeOutput = false;
        $setting = $this->settingsArray['jtl_eap_identcheck_use'];

        if ($setting == 0 && !$this->requireFullIDCard()) {
            return;
        }
        if (!$this->identCheckAlwaysOrAttribute() && !$this->requireFullIDCard()) {
            return;
        }

        if ($this->requestParams['art'] == 'B2B') {
            return;
        }

        $adresschanged = $this->identCheckKundeAddressChange();
        if ($this->requestParams['Rechnungsadresse']->kKundengruppe > 0 && $this->functions->checkkundenGruppeall($this->requestParams['Rechnungsadresse']->kKundengruppe, $this->settingsArray['identCustomergroup']) && !$adresschanged) {
            // AUSGESCHLOSSENE KUNDENGRUPPE ABER NUR WENN EIN EINTRAG ZU DER GEPRÜFTEN ADRESSE EXISTIERT
            return;
        }

        if ($this->schufaIdent->requested && $this->schufaIdent->verified) {
            return;
        }

        if ($this->schufaIdent->requested != true && !$this->requireFullIDCard()) {
            $this->fetchIdentCheckTemplate();

            return;
        }

        if ($setting == 1 && $this->schufaIdent->requested == true && $this->schufaIdent->verified != true) {
            $this->smarty->assign('IDENT_FAILED', true);
            // NUR SCHUFA FAILED
            $this->smarty->assign('QBIT_FAILED', true);
            $this->smarty->assign('identcheck_failed_msg', $this->sprachArr['jtl_eap_identcheck_failed_msg']);
            $this->smarty->assign('identcheck_failed_headline', $this->sprachArr['jtl_eap_identcheck_failed_headline']);
            $this->smarty->assign('identcheck_qbit_output', $this->settingsArray['identcheck_qbit_output'] == 1 ? true : false);
            $this->smarty->assign('identcheck_qbit_dataerror_msg', $this->sprachArr['identcheck_qbit_dataerror_msg']);
            $this->smarty->assign('identcheck_qbit_dataerror', str_replace(')', '%)', $this->schufaIdent->responseData->dataerror));
            $this->fetchIdentCheckTemplate();

            return;
        }
    }

    public function setNoticeAgeCheck($warenkorb)
    {
        if ($this->settingsArray['jtl_eap_identcheck_use'] > 0) {
            $this->smarty->assign('agecheck_warenkorb_msg', $this->sprachArr['agecheck_warenkorb_msg']);
        }
        //$this->smarty->assign('alertmsg_shipping',$this->sprachArr['alertmsg_shipping']);
        //$this->smarty->assign('alert_warenkorb',$warenkorb);
    }

    public function disableShippingMethods()
    {
        if (count($this->settingsArray['identShipping']) > 0 && $this->settingsArray['jtl_eap_identcheck_use'] > 0) {
            $this->smarty->assign('alertmsg_shipping', $this->sprachArr['alertmsg_shipping']);
            $this->smarty->assign('disabled_shipping_methods', $this->settingsArray['identShipping']);
        }

        return count($this->settingsArray['identShipping']);
    }

    public function disablePaymentMethods()
    {
        $this->smarty->assign('alertmsg_payment', $this->sprachArr['alertmsg_payment']);
        $this->smarty->assign('secure_checkout_payment', true);
    }

    public function setFancyBoxIdentCheckFailed()
    {
        $this->smarty->assign('use_postident', true);
        $loadedTPL = "<div id='eap_fancyBoxIdentFailed' style=\"width:auto;height:auto;display:none\"><a id='fancyTrigger'>
									" . utf8_decode($this->smarty->fetch($this->pluginSettings->cFrontendPfad . 'tpl/fancyBoxIdentFailed.tpl')) .
                          '</div>';

        pq($this->pq_footer)->prepend($loadedTPL);

        $execute = "<script>$(document).ready(function() { 
		
		$.fancybox({
					'scrolling'     : 'no',
					'overlayOpacity': 0.9,
					'showCloseButton'   : true,
					'href' : '#eap_fancyBoxIdentFailed'            
				});
		
		});</script>";
        pq($this->pq_footer)->prepend($execute);
    }

    public function setIdentCheckWall()
    {
        $this->RemovePaymentWallSetIdentCheckWall();
    }

    public function setPaymentWallFancyBox($type, $currentHandle)
    {
        $this->smarty->assign('b2c_birthday', $this->settingsArray['b2c_birthday']);

        $kundengruppenregeln = new stdClass();
        $kundengruppenregeln->nBoni = $this->functions->checkKundengruppeAll($this->requestParams['Rechnungsadresse']->kKundengruppe, $this->settingsArray['boniCustomergroup']) ? 1 : 0;

        if ($this->settingsArray['jtl_eap_cardprotection'] > 0 && $this->changed_card + 1 >= ($this->settingsArray['jtl_eap_cardprotection'] + 2)) {
            $this->disablePaymentMethods();

            return;
        }

        if ($type->LOG_NAME == 'Bonitätsprüfung' && $kundengruppenregeln->nBoni == 1) {
            return;
        }

        if ($this->requestParams['Rechnungsadresse']->cLand != 'DE' && $this->sprachArr['jtl_eap_ausland'] == 1) {
            $this->disablePaymentMethods();

            return;
        }

        if ($this->requestParams['adresseAbweichend'] && $this->settingsArray['jtl_eap_abweichend']) {
            $this->smarty->assign('abweichend', true);
            $this->smarty->assign('abweichend_msg', $this->sprachArr['jtl_eap_abweichend_adresse']);
            $this->disablePaymentMethods();

            return;
        }

        if ($type->responseData == null || ($currentHandle != $type->handle && $type->RetryIfHandleChanged) || $type->warenkorb != $this->requestParams['Warenkorb'] && $type->RetryIfHandleChanged) { // NOCH KEIN ERGEBNIS , ODER  UNGLEICHER HANDLE --> Template bei aktivierten Zahlarten laden
            $type->requested = null;
            $type->responseData = null;
            $type->handle = null;
            $type->warenkorb = null;
        } elseif ($type->responseData->secure_payment) {
            $this->disablePaymentMethods(false);
        }
    }
}
