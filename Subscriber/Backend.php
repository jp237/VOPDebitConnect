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

namespace VOPDebitConnect\Subscriber;

use Enlight\Event\SubscriberInterface;
use mysql_xdevapi\Exception;

class Backend implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {

        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_VOPDebitConnect' => array('onGetControllerPathBackend',0)
        ];
    }

    public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args)
    {

        return __DIR__ . '/../Controllers/Backend/VOPDebitConnect.php';
    }




}
