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
class EAP_Bonitaetspruefung
{
    public $vopWSDL = 'https://api.eaponline.de/bonigateway.php?wsdl';
    public $init;
    public $initHandle;
    public $enabled;
    public $handle;
    public $warenkorb;
    public $requestParams;
    public $responseData;
    public $authData;
    /** @var EAP_Functions */
    public $functions;
    public $pluginSettings;
    public $settingsArray;
    public $sprachArr;
    public $smarty;
    public $RetryIfHandleChanged = true;
    public $LOG_NAME = 'Bonitätsprüfung';
    public $current_id;

    public function __construct($functions)
    {
        $this->functions = $functions;
    }

    public function setNonRequestResponse($secure_payment){
        $this->responseData = new Model\BoniGatewayResultResponse();
        $this->responseData->setSecurePayment($secure_payment);
        $this->requested = true;
    }

    public function doRequest($handle)
    {
        $warenkorb = $this->requestParams['Warenkorb'];

        if (($this->handle != null && $this->handle == $handle) && ($this->warenkorb != null && $this->warenkorb == $warenkorb)) {
            return;
        }

        if (!$this->functions->istGesperrt($this->requestParams['Zahlungsart']->kZahlungsart, $this->settingsArray['boniPayments'])) {
            return;
        }

        if ($this->requestParams['Rechnungsadresse']->cLand != 'DE' && $this->settingsArray['jtl_eap_ausland'] == 2) {
            return;
        }

        $Rechnungsadresse = $this->functions->clearDataRechnungsadresse($this->requestParams['Rechnungsadresse']);
        $Lieferadresse = $this->functions->clearDataRechnungsadresse($this->requestParams['Lieferadresse']);

        if ($this->functions->checkKundengruppeAll($this->requestParams['Rechnungsadresse']->kKundengruppe, $this->settingsArray['boniCustomergroup'])) {
            return;
        }

        $this->requested = null;
        $this->responseData = null;
        $this->handle = null;
        $this->warenkorb = null;

        $this->handle = $handle;
        $this->warenkorb = $warenkorb;

        $e = null;
        $request = $Rechnungsadresse;
        if($this->requestParams["art"] == "B2C"  )
        {
            $settings  =  $this->settingsArray['jtl_eap_b2c'];

            if($settings==1){
                $this->setNonRequestResponse(true);
            }
            else  if($settings==2){
                $this->setNonRequestResponse(true);
            }
            else
            {
                try
                {


                    $config = EAPBoniGateway::getConfigWithToken(EAPBoniGateway::getAccessToken($this->settingsArray,$this->functions->shopId));
                    $api = new \VOP\Rest\Api\BonigatewaycustomerApi(     new GuzzleHttp\Client(), $config);


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
                    try{
                        $dt = new DateTime($request["geb"]);
                        $addressData->setDateofbirth($request["geb"] );
                    }catch(Exception $ee){
                        $addressData->setDateofbirth("00.00.0000");
                    }

                    $project->setBasketAmount($warenkorb);

                    $params->setAdressdata($addressData);
                    $params->setRequest($project);


                    $result = $api->bonigatewayCustomerGetRequestCustomerGetDocumentPost("false", $params);



                    if($result->getInternalId()>0) $this->current_id = $result->getInternalId();
                    $this->responseData = $result;

                    $this->requested = true;
                }catch(Exception $e)
                {
                    $this->setNonRequestResponse(true);

                    $this->functions->ExceptionLogger($this->LOG_NAME,$e,$this->settingsArray,$this->sprachArr);

                    if($this->settingsArray['jtl_eap_exception_boni_action']>0){
                        $this->setNonRequestResponse(false);
                    }
                }
            }

        }
        else
        {

            if($this->settingsArray['jtl_eap_b2b']==1){
                $this->setNonRequestResponse(true);
            }
            else  if($this->settingsArray['jtl_eap_b2b']==2){
                $this->setNonRequestResponse(false);
            }
            else
            {

                try
                {

                    $config = EAPBoniGateway::getConfigWithToken(EAPBoniGateway::getAccessToken($this->settingsArray));

                    $businessApi = new Api\BonigatewaybusinessApi(new GuzzleHttp\Client(),$config);


                    $params = new Model\GetRequestCustomerInputParameters();
                    $project = new Model\GatewayRequestParameters();
                    $addressData = new Model\AddressData();

                    $addressData->setCompany($request["Firma"]);
                    $addressData->setZipcode($request["PLZ"]);
                    $addressData->setStreet($request["Strasse"]);
                    $addressData->setCountry($request["land"]);
                    $addressData->setCity($request["Ort"]);
                    $addressData->setSalutation($request["geschlecht"]);

                    $project->setBasketAmount($warenkorb);

                    $params->setAdressdata($addressData);
                    $params->setRequest($project);


                    $result =  $businessApi->bonigatewayBusinessGetRequestBusinessGetDocumentResellingPost("false","false",$params);
                    $this->responseData = $result;
                    $this->requested = true;

                }catch(Exception $e)
                {

                    $this->setNonRequestResponse(true);
                    $this->functions->ExceptionLogger($this->LOG_NAME,$e,$this->settingsArray,$this->sprachArr);
                    if($this->settingsArray['jtl_eap_exception_boni_action']>0){
                        $this->setNonRequestResponse(false);
                    }

                }

            }
        }
        $this->functions->createLog($this,$e);
    }

    public function checkEnabled($oPlugin, $smarty, $settings)
    {
        $this->pluginSettings = $oPlugin;
        $this->smarty = $smarty;
        $this->settingsArray = $settings;
        $this->enabled = true;
        $this->sprachArr = null;

        return true;
    }
}
