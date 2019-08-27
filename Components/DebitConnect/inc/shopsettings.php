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

class shopsettings
{
    public $selectedShop;
    public $settings;
    public $shopsArray;
    public $shops;
    public $currentSetting;
    public $currentStates;
    public $currentPayments;
    public $currentVorkasse;
    public $currentSEPA;
    public $currentHBCI;
    public $hbciProfiles;
    public $mahnstopCustomerGroup;
    public $SKRSkonto;
    public $registration;
    public $cronjob;
    public $hbciBlacklist;
    public $shipping;

    public $hbciMailCustomerGroupDisable = null;

    public function __construct($db)
    {
        //$this->refresh($db,DC()->shopList);
    }

    public function flushsettings()
    {
        $this->mahnstopCustomerGroup = null;
        $this->currentSetting = null;
        $this->currentStates = null;
        $this->currentPayments = null;
        $this->currentVorkasse = null;
        $this->currentHBCI = null;
        $this->SKRSkonto = null;
        $this->settings = null;
        $this->cronjob = null;
        $this->shipping = null;
        $this->hbciBlacklist = null;
        $this->hbciMailCustomerGroupDisable = null;
    }

    public function refresh($db, $shopList)
    {
        $this->getRegistration($db);
        $this->getCompanySettings($db, $shopList);
        $this->getActiveSettings();
    }

    public function getRegistration($db)
    {
        $this->shopsArray = $db->getSQLResults('select * from dc_firma');
    }

    public function getCompanySettings($db, $shopList)
    {
        if (isset(DC()->shopList)) {
            foreach (DC()->shopList as $shopsettings) {
                if ($shopsettings['settings'] < 1) {
                    //DC()->getConf("mainsettings","{\"fristZE\":\"14\",\"zeArt\":\"2\",\"fristMA\":\"7\",\"shopwareapi\":\"\"}");
                }
            }
        }
    }

    public function getHBCIProfiles()
    {
        $shop = $this->selectedShop;
        $profiles = DC()->db->getSQLResults('SELECT * from dc_hbciprofiles where shopID = ' . $shop);
        $hbciProfiles = [];
        foreach ($profiles as $profil) {
            $newProfil = new stdClass();
            $newProfil->id = $profil['id'];
            $newProfil->profileName = $profil['profileName'];
            $newProfil->profileData = json_decode(DebitConnectCore::decrypt($profil['profileData']));
            $hbciProfiles[$newProfil->id] = $newProfil;
        }

        return $hbciProfiles;
    }

    public function updateProfile($profilId, $formData)
    {
        $shop = $this->selectedShop;
        $update = new stdClass();

        $update->profileData = DebitConnectCore::encrypt(json_encode($formData));
        DC()->db->dbUpdate('dc_hbciprofiles', $update, 'id = ' . (int) $profilId);
    }

    public function getHBCIsettings($object)
    {
        $values = [
                        'betreff' => $object->betreff,
                        'absender' => $object->absender,
                        'bestaetigung' => $object->bestaetigung,
                        'statusbezahlt' => $object->statusbezahlt,
                        'orderstatus' => $object->orderstatus,
                        'teilzahlung' => $object->teilzahlung,
                        'setpaymentdate' => $object->setpaymentdate,
                        'bankruecklast' => $object->bankruecklast,
                        'zahlungsausgang' => $object->zahlungsausgang, ];

        //DC()->hbci->setHBCIData($values["url"],$values["blz"],$values["alias"],$values["pin"]);
        return $values;
    }

    public function getSKR()
    {
        $this->SKRSkonto['skonto'] = json_decode(DC()->getConf('conf_skonto', ''));
        $this->SKRSkonto['zeitraum'] = json_decode(DC()->getConf('conf_skonto_zeitraum', ''));
        $this->SKRSkonto['skr_payment'] = json_decode(DC()->getConf('conf_skr_payment', ''));
        $this->SKRSkonto['skr_buchungpos'] = json_decode(DC()->getConf('conf_skr_buchungpos', ''));
    }

    public function getActiveSettings()
    {
        //foreach($this->settings as $setting)
        //	{
        //if($setting['shopID'] == $this->selectedShop)
        //	{
        $this->registration = DC()->db->singleResult(' SELECT * from dc_firma where shopID = ' . (int) $this->selectedShop);
        $this->currentSetting = json_decode(DC()->getConf('mainsettings', '{"fristZE":"14","zeArt":"2","fristMA":"7","shopwareapi":""}'));
        $this->currentStates = json_decode(DC()->getConf('states', ''));
        $this->currentPayments = json_decode(DC()->getConf('payment', ''));
        $this->currentVorkasse = json_decode(DC()->getConf('vorkasse', ''));
        $this->currentSEPA = json_decode(DC()->getConf('sepa', ''));
        $this->shipping = json_decode(DC()->getConf('shipping', '{ "overrideInvoice" : 0 , "states" : [7] }'));
        $this->mahnstopCustomerGroup = json_decode(DC()->getConf('mahnstopCustomerGroup', ''));
        $this->cronjob = json_decode(DC()->getConf('cronjob', ''));
        $this->currentHBCI = $this->getHBCIsettings(json_decode(DC()->getConf('hbci', '')));
        $this->getSKR();
        $this->hbciProfiles = $this->getHBCIProfiles((int) $this->selectedShop);
        $this->hbciBlacklist = json_decode(DC()->getConf('hbciBlacklist', json_encode([]), true));
        $this->hbciMailCustomerGroupDisable = json_decode(DC()->getConf('hbciCustomerGroup', json_encode([]), false));
        //if(($this->currentPayments) == 0)$this->currentPayments[] = false;
                //if(count($this->currentStates) == 0) $this->currentStates[] = false;

        //	}
        //}
    }
}
