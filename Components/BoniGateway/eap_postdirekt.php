<?php
/**
 * Created by PhpStorm.
 * User: J.Perzewski
 * Date: 25.02.2019
 * Time: 11:56
 */

class eap_postdirekt
{
    /** @var EAP_Functions */
    var $functions= null;
    var $requested_handle = null;
    var $isAccepted = false;
    var $enabled = true;
    var $response = null;

    var $shipping_adress_overriden = false;
    var $invoice_adress_overridden = false;
    function __construct($functions) {
        $this->functions = $functions;
    }

    function checkEnabled(){
        $this->enabled = true;
        return $this->enabled;
    }


}