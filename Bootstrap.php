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

use VOPDebitConnect\Subscriber\Backend;
use VOPDebitConnect\Subscriber\Cronjob;
use VOPDebitConnect\Subscriber\Frontend;
use VOPDebitConnect\Subscriber\Order;


require_once __DIR__ . '/Components/CSRFWhitelistAware.php';
require_once __DIR__ . '/Components/Services/Cronjob.php';
require_once __DIR__ . '/Subscriber/Backend.php';
require_once __DIR__ . '/Subscriber/Frontend.php';
require_once __DIR__ . '/Subscriber/Order.php';
require_once __DIR__ . '/Subscriber/Cronjob.php';

class Shopware_Plugins_Backend_VOPDebitConnect_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    const TEMPLATE_PATH = 'Views/';



    /**
     * Returns an array with the capabilities of the plugin.
     *
     * @return array
     */
    public function getCapabilities()
    {
        return [
            'install' => true,
            'enable' => true,
            'update' => true,
        ];
    }

    //Internals STOP

    //Informations START
    public function getVersion()
    {
        return $this->GetPluginInformationValue('currentVersion');
    }

    public function getLabel()
    {
        return $this->GetPluginInformationValue('label')['de'];
    }

    public function getInfo()
    {
        return [
            'label' => $this->getLabel(),
            'version' => $this->getVersion(),
            'copyright' => $this->GetPluginInformationValue('copyright'),
            'author' => $this->GetPluginInformationValue('author'),
            'link' => $this->GetPluginInformationValue('link'),
            'support' => $this->GetPluginInformationValue('support'),
            'description' => $this->GetPluginInformationValue('description'),
        ];
    }

    public function afterInit()
    {

        $this->registerCustomModels();

    }

    /**
     * Install function of the plugin bootstrap.
     *
     * Registers all necessary components and dependencies.
     *
     * @return bool
     */
    public function update($version)
    {
        $install = $this->runInstallUpdate($version);
        if ($install === true) {
            return true;
        }

        return ['success' => false, 'message' => $install];
    }

    /*	  public function afterInit() {
          $this->Application()->Loader()->registerNamespace('Shopware\BoniGateway', $this->Path());
        }
    */

    public function installRemoveCronJobs($cronjob, $install = true)
    {
        if ($install) {
            Shopware()->Db()->insert(
                's_crontab',
                [
                    'name' => $cronjob['name'],
                    'action' => $cronjob['action'],
                    'next' => date('Y-m-d H:i:s'),
                    'start' => date('Y-m-d H:i:s'),
                    'interval' => $cronjob['interval'],
                    'active' => 0,
                    'pluginID' => null,
                ]
            );
        } else {
            Shopware()->Db()->query("delete from s_crontab where `name` = '" . $cronjob['name'] . "' and action = '" . $cronjob['action'] . "'");
        }
    }

    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->Application()->Loader()->registerNamespace('\VOPDebitConnect', $this->Path());

        /** @var Shopware\Components\DependencyInjection\Container $container */
        $container = Shopware()->Container();

        $subscribers = [
            new Backend(),
            new Frontend(),
            new Order(),
            new Cronjob(),
        ];
        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
    }

    public function runInstallUpdate($version = '0.0.0')
    {
        $intVersion = (int) str_replace('.', '', $version);

        if (version_compare($version, '0.0.1', '<')) {
            $this->subscribeEvent(
                'Enlight_Controller_Front_DispatchLoopStartup',
                'onStartDispatch'
            );
            $this->registerController('Backend', 'VOPDebitConnect');
            $this->createMenu();
        }

        if (version_compare($version, '0.2.4', '<')) {
            $this->installRemoveCronJobs(['name' => 'DebitConnect',
                'action' => 'Shopware_CronJob_VOPCronjob',
                'interval' => 600, ]);
        }

        return true;
    }

    public function install()
    {
        try {
            $install = $this->runInstallUpdate();
            if ($install === true) {
                return [
                    'success' => true,
                    'invalidateCache' => ['backend','frontend'],
                ];
            }

            return ['success' => false, 'message' => $install];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Creates the Favorites backend menu item.
     *
     * The Favorites menu item opens the listing for the SwagFavorites plugin.
     */
    public function createMenu()
    {
        $this->createMenuItem([
            'label' => 'DebitConnect',
            'active' => 1,
            'onclick' => 'Shopware.ModuleManager.createSimplifiedModule("VOPDebitConnect", { "title": "DebitConnect" })',
        ]);
    }

    /**
     * Uninstall function of the plugin.
     * Fired from the plugin manager.
     *
     * @return bool
     */
    public function uninstall()
    {
        Shopware()->BackendSession()->DebitConnect = null;
        // REMOVE CRONJOBS
        $this->installRemoveCronJobs(['name' => 'DebitConnect',
            'action' => 'Shopware_CronJob_VOPCronjob',
            'interval' => 3600, ], false);

        return [
            'success' => true,
            'invalidateCache' => ['backend','frontend'],
        ];
    }

    public function disable(){
        return [
            'success' => true,
            'invalidateCache' => ['backend','frontend'],
        ];
    }

    public function DebitConnectZahlungsabgleich(Shopware_Components_Cron_CronJob $job)
    {
        return __DIR__ . '/Controller/VOPCronjob.php';
    }

    /**
     * After init event of the bootstrap class.
     *
     * The afterInit function registers the custom plugin models.
     */
    private function GetPluginInformationValue($sValueName)
    {
        $pluginJSON = json_decode(file_get_contents($this->Path() . 'plugin.json'), true);

        if ($pluginJSON) {
            return $pluginJSON[$sValueName];
        }
        throw new Exception('The plugin has an invalid plugin.json file.');
    }
}
