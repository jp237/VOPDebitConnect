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

        if ($this->requestParams['art'] == 'B2C') {
            $settings = $this->settingsArray['jtl_eap_b2c'];

            if ($settings == 1) {
                $this->requested = true;
                $this->responseData->secure_payment = true;
            } elseif ($settings == 2) {
                $this->requested = true;
                $this->responseData->secure_payment = false;
            } else {
                try {
                    $client = new SoapClient($this->vopWSDL, ['cache_wsdl' => WSDL_CACHE_NONE]);
                    $result = $client->getSCHUFAB2C($this->settingsArray['jtl_eap_userid'],md5($this->settingsArray['jtl_eap_passwort']),'0','shoplogin',
                                        $request['Vorname'],$request['Nachname'],$request['geschlecht'],$request['geb'],
                                        $request['Strasse'], $request['PLZ'], $request['Ort'], $request['mail'], $request['tel'], $request['ip'], $warenkorb, 'FALSE', '', '', '', '', $request['land'], $this->requestParams['Zahlart']->cName, '', '');

                    if ($result->nachbehandlungID > 0) {
                        $this->current_id = $result->nachbehandlungID;
                    }
                    $this->responseData = $result;
                    $this->requested = true;
                } catch (Exception $e) {
                    $this->requested = true;
                    $this->responseData->secure_payment = true;
                    $this->functions->ExceptionLogger($this->LOG_NAME, $e, $this->settingsArray, $this->sprachArr);
                    if ($this->settingsArray['jtl_eap_exception_boni_action'] > 0) {
                        $this->responseData->secure_payment = false;
                        $this->requested = true;
                    }
                }
            }
        } else {
            if ($this->settingsArray['jtl_eap_b2b'] == 1) {
                $this->requested = true;
                $this->responseData->secure_payment = true;
            } elseif ($this->settingsArray['jtl_eap_b2b'] == 2) {
                $this->requested = true;
                $this->responseData->secure_payment = false;
            } else {
                try {
                    $client = new SoapClient($this->vopWSDL, ['encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE]);

                    $result = $client->getSCHUFAB2B($this->settingsArray['jtl_eap_userid'],md5($this->settingsArray['jtl_eap_passwort']),'0','shoplogin',
                                            $request['Firma'], '', $request['Strasse'], $request['PLZ'], $request['Ort'], $request['land'], $this->requestParams['Zahlart']->cName, '', $warenkorb);
                    if ($result->nachbehandlungID > 0) {
                        $this->current_id = $result->nachbehandlungID;
                    }
                    $this->responseData = $result;
                    $this->requested = true;
                } catch (Exception $e) {
                    $this->requested = true;
                    $this->responseData->secure_payment = true;
                    $this->functions->ExceptionLogger($this->LOG_NAME, $e, $this->settingsArray, $this->sprachArr);
                    if ($this->settingsArray['jtl_eap_exception_boni_action'] > 0) {
                        $this->responseData->secure_payment = false;
                        $this->requested = true;
                    }
                }
            }
        }

        $this->functions->createLog($this, $e);
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
