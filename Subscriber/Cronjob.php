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

class Cronjob implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_CronJob_VOPCronjob' => 'CronJobAction',
            'Enlight_Bootstrap_InitResource_vopdebitconnect.runcronjob' => 'runCronjobService',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_VOPCronjob' => 'onGetControllerPathBackend',
        ];
    }

    public function runCronjobService(\Enlight_Event_EventArgs $args)
    {
        return new \VOPDebitConnect\Components\Cronjob();
    }

    public function CronJobAction(\Enlight_Event_EventArgs $args)
    {
        // CREATE NEW SMARTY INSTANCE FOR SERVICE runcronjob
        $view = new \Enlight_View_Default(Shopware()->Container()->get('template'));
        $cronTask = Shopware()->Container()->get('vopdebitconnect.runcronjob');

        return $cronTask->getCronjobTask($view);
    }

    public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controllers/Backend/VOPCronjob.php';
    }
}
