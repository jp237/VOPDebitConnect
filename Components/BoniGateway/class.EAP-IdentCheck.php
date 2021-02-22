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
use  \VOP\Rest\Model;
use  \VOP\Rest\Api;
class EAP_IdentCheck
{
    public $vopWSDL = 'https://api.eaponline.de/bonigateway.php?wsdl';
    public $init;
    public $enabled;
    public $handle;
    public $requested;
    public $requestParams;
    public $responseData;
    public $authData;
    public $functions;
    public $pluginSettings;
    public $settingsArray;
    public $sprachArr;
    public $smarty;
    public $RetryIfHandleChanged = true;
    public $verified;
    public $secure_payments;
    public $LOG_NAME = 'IdentCheck';

    public function __construct($functions)
    {
        $this->functions = $functions;
    }

    public function doRequest($requestparams)
    {


        $request = $this->functions->clearDataRechnungsadresse($requestparams["Rechnungsadresse"]);

        if($this->requested) return;
        $e = null;
        try
        {


            $api = new \VOP\Rest\Api\BonigatewaycustomerApi(
                new GuzzleHttp\Client(),EAPBoniGateway::getConfigWithToken(EAPBoniGateway::getAccessToken($this->settingsArray))
            );



            $params = new Model\GetRequestCustomerInputParameters();
            $project = new Model\GatewayRequestParameters();
            $addressData = new Model\AddressData();


            $addressData->setFirstname($request["Vorname"]);
            $addressData->setLastname($request["Nachname"]);
            $addressData->setZipcode($request["PLZ"]);
            $addressData->setStreet($request["Strasse"]);
            $addressData->setCountry($request["land"]);
            $addressData->setCity($request["Ort"]);
            $addressData->setSalutation($request["geschlecht"]);
            $addressData->setDateofbirth($request["geb"]);


            $params->setAdressdata($addressData);
            $params->setRequest($project);



            $result =  $api->bonigatewayCustomerVerifyAgeGetDocumentPost("false",$params);


            $this->responseData = $result;



            $this->completed = true;
            $this->requested = true;
            $this->functions->verifiedAndMovedToKundengruppe($this);
            $this->handle = $requestparams["currenthandle"];

        }
        catch(Exception $e)
        {
            $this->functions->ExceptionLogger("Identcheck",$e,$this->settingsArray,$this->sprachArr);
            $this->setNonRequestResponse(true);

        }

        $this->functions->createLog($this,$e);

    }

    public function setNonRequestResponse($secure_payment){
        $this->responseData = new Model\BoniGatewayResultResponse();
        $this->responseData->setSecurePayment($secure_payment);
        $this->requested = true;
    }

    public function checkEnabled($oPlugin, $smarty, $settings, $requestparams)
    {
        $this->requestParams = $requestparams;
        if ($this->requestParams['art'] == 'B2B') {
            return false;
        }

        $this->pluginSettings = $oPlugin;
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
