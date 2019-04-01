<?php

class EAP_IdentCheck{
	var $vopWSDL = "https://api.eaponline.de/bonigateway.php?wsdl";
	var $init;
	var $enabled;
	var $handle;
	var $requested;
	var $requestParams;
	var $responseData;
	var $authData;
	var $functions;
	var $pluginSettings;
	var $settingsArray;
	var $sprachArr;
	var $smarty;	
	var $RetryIfHandleChanged = true;
	var $verified;
	var $secure_payments;
	var $LOG_NAME = "IdentCheck";
	  function __construct($functions) {
	  $this->functions = $functions;
   }
   
   public function doRequest($requestparams)
   {

	$request = $this->functions->clearDataRechnungsadresse($requestparams["Rechnungsadresse"]);
	if($this->requested) return;
	$e = null;
	 try
		{
			$client = new SoapClient($this->vopWSDL,array(  'encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE));
			$result = $client->getSCHUFAIdent($this->settingsArray['jtl_eap_userid'],md5($this->settingsArray['jtl_eap_passwort']),$request['Vorname'],$request['Nachname'],$request['geschlecht'],$request['geb'],$request['Strasse'],$request['PLZ'],$request['Ort'],$request['land'],"Premium","18");
			$this->responseData = $result;
			$this->secure_payments = $this->responseData->verified == false ? true: false;
			$this->verified = $this->responseData->verified;
			$this->responseData->secure_payment = $this->secure_payments;
			$this->completed = true;
			$this->requested = true;
			$this->functions->verifiedAndMovedToKundengruppe($this);
			$this->handle = $requestparams["currenthandle"];

		}
			catch(Exception $e)
		{ 
		$this->functions->ExceptionLogger("Identcheck",$e,$this->settingsArray,$this->sprachArr);
		$this->responseData->secure_payment = true;
		$this->secure_payments = false;
		$this->responseData->error = true;
		$this->responseData->verified = false;
			
		}
		
	  $this->functions->createLog($this,$e);
	  
   }
	  public function checkEnabled($oPlugin,$smarty,$settings,$requestparams)
   {	
  
   		$this->requestParams = $requestparams;
		if($this->requestParams["art"] == "B2B"){ return false;}

	   	$this->pluginSettings =  $oPlugin;
	   	$this->smarty = $smarty;
		$this->settingsArray = $settings;
	   	$this->enabled = true;
	   	$this->sprachArr = null;
	 	/*$oPluginSprache = gibSprachVariablen($this->pluginSettings->kPlugin);
		$cLang = strtoupper($this->smarty->get_template_vars('lang'));
		
		foreach($oPluginSprache as $key)
		{
		 $this->sprachArr[$key->cName] = $key->oPluginSprachvariableSprache_arr[$cLang];
		}*/
		return true;
	   
   }
   
  
}
