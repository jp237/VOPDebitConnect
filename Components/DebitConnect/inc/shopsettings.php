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
        $std = DebitConnectCore::encrypt(
            json_encode(new hbciProfile())
        );
       $profile = DC()->getConf("finapi",$std);
       $profile = DC()->castJson(new hbciProfile(),
           json_decode(DebitConnectCore::decrypt($profile)));
       $profile->bankAccounts = json_decode(json_encode($profile->bankAccounts),true);
        $profile->dtaInformation = json_decode(json_encode($profile->dtaInformation),true);
        return $profile;
    }

    public function updateProfile(hbciProfile $currentProfil)
    {
        $formData = DC()->get('profile');
        if(DC()->hasvalue('saveLoginCredentials')){
            $currentProfil->client_secret = $formData["client_secret"];
            $currentProfil->client_id = $formData["client_id"];
            $currentProfil->url = $formData["url"];
        }


        if(DC()->hasvalue('saveAccount')){
            $accountData = DC()->get('saveAccount');
            $bank = key($accountData);
            $currentProfil->bankAccounts[$bank] = $formData["accounts"];
        }

        if(isset($formData["dtaInformation"])){
            $currentProfil->dtaInformation = $formData["dtaInformation"];
        }

        DC()->setConf("finapi",DebitConnectCore::encrypt(json_encode($currentProfil)));

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

        $this->hbciBlacklist = json_decode(DC()->getConf('hbciBlacklist', json_encode([]), true));
        $this->hbciMailCustomerGroupDisable = json_decode(DC()->getConf('hbciCustomerGroup', json_encode([]), false));
       
    }
}
