<?php
class shopsettings {

	
	var $selectedShop;
	var $settings;
	var $shopsArray;
	var $shops;
	var $currentSetting;
	var $currentStates;
	var $currentPayments;
	var $currentVorkasse;
	var $currentSEPA;
	var $currentHBCI;
	var $hbciProfiles;
	var $mahnstopCustomerGroup;
	var $SKRSkonto;
	var $registration;
	var $cronjob;
	var $hbciBlacklist;
	var $shipping;

	var $hbciMailCustomerGroupDisable = null;
	function flushsettings()
	{
		$this->mahnstopCustomerGroup= null;
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
	function refresh($db,$shopList)
	{

		$this->getRegistration($db);
		$this->getCompanySettings($db,$shopList);
		$this->getActiveSettings();
	}
	
	function getRegistration($db)
	{
		$this->shopsArray = $db->getSQLResults("select * from dc_firma");
	}
	
	function getCompanySettings($db,$shopList)
	{		
	if(isset(DC()->shopList)){
		foreach(DC()->shopList as $shopsettings)
		{
			if($shopsettings['settings']<1)
			{
				//DC()->getConf("mainsettings","{\"fristZE\":\"14\",\"zeArt\":\"2\",\"fristMA\":\"7\",\"shopwareapi\":\"\"}");
			}
		}
	}
	}
	
	function getHBCIProfiles(){
		$shop = $this->selectedShop;
		$profiles = DC()->db->getSQLResults("SELECT * from dc_hbciprofiles where shopID = ".$shop);
		$hbciProfiles = array();
		foreach($profiles as $profil){
			$newProfil = new stdClass();
			$newProfil->id = $profil['id'];
			$newProfil->profileName = $profil['profileName'];
			$newProfil->profileData = json_decode(DebitConnectCore::decrypt($profil['profileData']));
			$hbciProfiles[$newProfil->id] = $newProfil;
		}

		return $hbciProfiles;
	}
	
	function updateProfile($profilId,$formData){
		$shop = $this->selectedShop;
		$update = new stdClass();
		
		$update->profileData = DebitConnectCore::encrypt(json_encode($formData));
		DC()->db->dbUpdate("dc_hbciprofiles",$update,"id = ".(int)$profilId);
	}
	function getHBCIsettings($object)
	{
		
		$values =  array(
						"betreff" => $object->betreff,
						"absender" => $object->absender,
						"bestaetigung" => $object->bestaetigung,
						"statusbezahlt" => $object->statusbezahlt,
						"orderstatus" => $object->orderstatus,
						"teilzahlung" => $object->teilzahlung,
						"setpaymentdate" => $object->setpaymentdate,
						"bankruecklast" => $object->bankruecklast,
                        "zahlungsausgang" => $object->zahlungsausgang);
						
		//DC()->hbci->setHBCIData($values["url"],$values["blz"],$values["alias"],$values["pin"]);
		return $values;	
	}
	
	function getSKR()
	{
		$this->SKRSkonto["skonto"] =  json_decode(DC()->getConf("conf_skonto",""));	
		$this->SKRSkonto["zeitraum"] =  json_decode(DC()->getConf("conf_skonto_zeitraum",""));	
		$this->SKRSkonto["skr_payment"] =  json_decode(DC()->getConf("conf_skr_payment",""));	
		$this->SKRSkonto["skr_buchungpos"] =  json_decode(DC()->getConf("conf_skr_buchungpos",""));	
	}
	function getActiveSettings()
	{
		//foreach($this->settings as $setting)
	//	{
			//if($setting['shopID'] == $this->selectedShop)
		//	{
				$this->registration = DC()->db->singleResult(" SELECT * from dc_firma where shopID = ".(int) $this->selectedShop);
				$this->currentSetting = json_decode(DC()->getConf("mainsettings","{\"fristZE\":\"14\",\"zeArt\":\"2\",\"fristMA\":\"7\",\"shopwareapi\":\"\"}"));
				$this->currentStates = json_decode(DC()->getConf("states",""));
				$this->currentPayments = json_decode(DC()->getConf("payment",""));
				$this->currentVorkasse = json_decode(DC()->getConf("vorkasse",""));
				$this->currentSEPA = json_decode(DC()->getConf("sepa",""));
				$this->shipping = json_decode(DC()->getConf('shipping','{ "overrideInvoice" : 0 , "states" : [7] }'));
				$this->mahnstopCustomerGroup = json_decode(DC()->getConf("mahnstopCustomerGroup",""));
				$this->cronjob = json_decode(DC()->getConf("cronjob",""));
				$this->currentHBCI = $this->getHBCIsettings(json_decode(DC()->getConf("hbci","")));
				$this->getSKR();
				$this->hbciProfiles = $this->getHBCIProfiles((int) $this->selectedShop);
				$this->hbciBlacklist = json_decode(DC()->getConf("hbciBlacklist",json_encode(array()),true));
                $this->hbciMailCustomerGroupDisable = json_decode(DC()->getConf("hbciCustomerGroup",json_encode(array()),false));
				//if(($this->currentPayments) == 0)$this->currentPayments[] = false;
				//if(count($this->currentStates) == 0) $this->currentStates[] = false;
			
		//	}
		//}
	}
	function __construct($db)
	{
	//$this->refresh($db,DC()->shopList);	
	}
}
?>