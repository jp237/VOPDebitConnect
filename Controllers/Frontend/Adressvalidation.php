<?php
/**
 * Created by PhpStorm.
 * User: J.Perzewski
 * Date: 26.02.2019
 * Time: 08:07
 */



class Shopware_Controllers_Frontend_Adressvalidation extends Enlight_Controller_Action
{


    function GatewaySettings(){
        try
        {
            $settings = array();
            $shop = Shopware()->Shop()->getMain() !== null ? Shopware()->Shop()->getMain() : Shopware()->Shop();
            $selectedShop = $shop->getId();

            $entrys =  Shopware()->Db()->fetchall("SELECT art,datavalue from dc_gatewaymeta where nType = 0 and shopID = ".$selectedShop);
            foreach($entrys as $entry){
                $settings[$entry['art']] = is_object(json_decode($entry['datavalue'])) ? json_decode($entry['datavalue'],true) : $entry['datavalue'];
            }

            return $settings;
        }catch(Exception $e){

            return null;
        }
    }

    public function getLanguageVars(){
        $settings = array();
        $shop = Shopware()->Shop()->getMain() !== null ? Shopware()->Shop()->getMain() : Shopware()->Shop();
        $selectedShop = $shop->getId();
        $entrys =  Shopware()->Db()->fetchall("SELECT art,datavalue from dc_gatewaymeta where nType = 1 and shopID = ".$selectedShop);
        foreach($entrys as $entry){
            $this->View()->assign($entry['art'],$entry['datavalue']);
        }

    }

    public function indexAction()
    {


        $sessionvalue =  Shopware()->Session()->eap_adressvalidation;
        $inputParams = $this->Request()->getParam('eap_adresscheck');
        $this->getLanguageVars();

        $inputHandle = md5(print_r($inputParams,true));
        $sessionhandle = null;
        if($sessionvalue != null){
            $sessionhandle = md5(print_r($sessionvalue["request"],true));
        }

        $settings = $this->GatewaySettings();

        header('Content-Type: application/json');
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
        $submit_address = $settings["postdirekt"] == 1 ? true : false;

        if($settings["postdirekt"] == 1 && $sessionhandle != $inputHandle && $settings != null) {
            try {
                $actionRequired = false;
                $responseBilling = array();
                $responseShipping = array();
                $requested = true;
                $soapclient = new SoapClient("https://api.eaponline.de/bonigateway.php?wsdl", array("trace" => 1, "encoding" => "utf-8"));
                if (count($inputParams["billing"] ) == 6) {
                    if ($inputParams["billing"]["country"] == 2) {
                        $responseBilling = $soapclient->getPostdirekt($settings["username"], md5($settings["passwd"]), $inputParams["billing"]["firstname"], $inputParams["billing"]["lastname"], $inputParams["billing"]["city"], $inputParams["billing"]["street"], $inputParams["billing"]["zipcode"]);
                        $responseBillingData = [
                            "firstname" => $submit_address ? $responseBilling->Vorname : $inputParams["billing"]["firstname"],
                            "lastname" => $submit_address ? $responseBilling->Nachname : $inputParams["billing"]["lastname"],
                            "street" => $responseBilling->Strasse,
                            "city" => $responseBilling->Ort,
                            "zipcode" => $responseBilling->PLZ,
                            "requestID" => $responseBilling->nachbehandlungID,
                            "correctionRequired" => !$responseBilling->secure_payment && $responseBilling->nachbehandlungID > 0 ? md5($inputParams["billing"]["city"].$inputParams["billing"]["street"].$inputParams["billing"]["zipcode"]) != md5($responseBilling->Ort.$responseBilling->Strasse.$responseBilling->PLZ) ? true : false : false,
                        ];
                    }
                }
                if (count($inputParams["shipping"]) == 7 && $inputParams["shipping"]["useShippingAdress"]) {
                    if ($inputParams["shipping"]["country"] == 2) {
                        $responseShipping = $soapclient->getPostdirekt($settings["username"], md5($settings["passwd"]), $inputParams["shipping"]["firstname"], $inputParams["shipping"]["lastname"], $inputParams["shipping"]["city"], $inputParams["shipping"]["street"], $inputParams["shipping"]["zipcode"]);
                        $responseShippingData = [
                            "firstname" => $submit_address ? $responseShipping->Vorname : $inputParams["shipping"]["firstname"],
                            "lastname" => $submit_address ? $responseShipping->Nachname : $inputParams["shipping"]["lastname"],
                            "street" => $responseShipping->Strasse,
                            "city" => $responseShipping->Ort,
                            "zipcode" => $responseShipping->PLZ,
                            "requestID" => $responseShipping->nachbehandlungID,
                            "correctionRequired" => !$responseShipping->secure_payment && $responseShipping->nachbehandlungID > 0 ? md5($inputParams["shipping"]["city"].$inputParams["shipping"]["street"].$inputParams["shipping"]["zipcode"]) != md5($responseShipping->Ort.$responseShipping->Strasse.$responseShipping->PLZ) ? true : false : false,
                        ];
                    }
                }

            } catch (Exception $e) {

            }

            $responseData = [
                "request" => $inputParams,
                "billing" => $responseBillingData,
                "shipping" => $responseShippingData,
                "requested" => $requested
            ];
        }else{
            // IF NO DATA HAS CHANGED RETURN SESSION STORED VALUE;
            $responseData = $sessionvalue;
        }

        $actionRequired = false;
            Shopware()->Session()->eap_adressvalidation = $responseData;
        $this->View()->assign('requestparams',$inputParams);

        $this->View()->assign('responseparams',$responseData);

        $smartyTpl = __DIR__."/../../Views/frontend/validation/template.tpl";
        $smartyTpl = file_get_contents($smartyTpl);

        $parsedTpl = $this->View()->fetch('string: '.$smartyTpl);
       // $parsedTPL = $this->View->fetch($smartyTpl);

        $array = [
            "actionRequired" => $responseData["billing"]["correctionRequired"] == true || $responseData["shipping"]["correctionRequired"] == true ? true : false,
                "htmlModal" => $parsedTpl,

        ];


        echo json_encode($array);
        exit;
    }

}
