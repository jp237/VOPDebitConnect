<?php
require_once __DIR__. '/vendor/autoload.php';
require_once __DIR__ . '/Components/CSRFWhitelistAware.php';
require_once __DIR__ . '/Components/Services/Cronjob.php';
require_once __DIR__.'/Subscriber/Backend.php';
require_once __DIR__.'/Subscriber/Frontend.php';
require_once __DIR__.'/Subscriber/Order.php';
require_once __DIR__.'/Subscriber/Cronjob.php';

/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
            'update' => true
        ];
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
        } else {
            throw new Exception('The plugin has an invalid plugin.json file.');
        }
	}
	//Internals STOP
	
	//Informations START	
	/*
	/**
 * checkLicense()-method for VOPDebitConnect
 */

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
		return array(
            'label' => $this->getLabel(),
            'version' => $this->getVersion(),
			'copyright' => $this->GetPluginInformationValue('copyright'),
			'author' => $this->GetPluginInformationValue('author'),
            'link' => $this->GetPluginInformationValue('link'),
			'support' => $this->GetPluginInformationValue('support'),
            'description' => $this->GetPluginInformationValue('description')
        );
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
		if($install===true)
		{
		 return true;
		}
		else
		{
			 return ['success' => false, 'message' => $install];
		}
	 }
	 
/*	  public function afterInit() {
      $this->Application()->Loader()->registerNamespace('Shopware\BoniGateway', $this->Path());
    }
*/

public function installRemoveCronJobs($cronjob,$install = true){
	if($install){
	  Shopware()->Db()->insert(
            's_crontab',
            [
                'name'             =>	$cronjob['name'],
                'action'           =>   $cronjob['action'],
                'next'             => date("Y-m-d H:i:s"),
                'start'            => date("Y-m-d H:i:s"),
                'interval'       => $cronjob['interval'],
                'active'           => 0,
                'pluginID'         => null
            ]
        );
	}else{
		Shopware()->Db()->query("delete from s_crontab where `name` = '".$cronjob['name']."' and action = '".$cronjob['action']."'");
	}
}
		
		public function onStartDispatch(Enlight_Event_EventArgs $args) {
			
			$this->Application()->Loader()->registerNamespace('\VOPDebitConnect',$this->Path());
			
			/** @var Shopware\Components\DependencyInjection\Container $container */
			$container = Shopware()->Container();
			
			
			$subscribers = [
				new \VOPDebitConnect\Subscriber\Backend(),
				new \VOPDebitConnect\Subscriber\Frontend(),
				new \VOPDebitConnect\Subscriber\Order(),
				new \VOPDebitConnect\Subscriber\Cronjob()
			];
			foreach ($subscribers as $subscriber) {
				$this->Application()->Events()->addSubscriber($subscriber);
			}

		
		}

	 
	
	
	 public function runInstallUpdate($version = "0.0.0")
	 {

		 $intVersion = (int) str_replace(".","",$version);
		 
		 if(version_compare($version ,"0.0.1" , "<"))
		 {
			 $this->subscribeEvent(
			'Enlight_Controller_Front_DispatchLoopStartup',
			'onStartDispatch'
		);
			 $this->registerController('Backend', 'VOPDebitConnect');
			 $this->createMenu();
		 }
		  
		  	 if(version_compare($version ,"0.2.4" , "<"))
		 {
			 
		
				
      		$this->installRemoveCronJobs(array("name" => "DebitConnect",
											"action" => "Shopware_CronJob_VOPCronjob",
											"interval" => 600));
			 
		 }
		 
		 return true;
	 }
	 
	 
    public function install()
    {
        try {
            $install = $this->runInstallUpdate();
          if( $install===true)
		  {
	
            return [
                'success' => true,
                'invalidateCache' => ['backend']
            ];
		  }else
		  {
			   return ['success' => false, 'message' => $install];
		  }
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
	$this->createMenuItem(array(
			'label' => 'DebitConnect',
			'active' => 1,
			'onclick' => 'Shopware.ModuleManager.createSimplifiedModule("VOPDebitConnect", { "title": "DebitConnect" })'
		));
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
		$this->installRemoveCronJobs(array("name" => "DebitConnect",
											"action" => "Shopware_CronJob_VOPCronjob",
											"interval" => 3600),false);
		
											
        return true;
    }
	

	 public function DebitConnectZahlungsabgleich(\Shopware_Components_Cron_CronJob $job)
    {	
		return dirname(__FILE__).'/Controller/VOPCronjob.php';
    }
	
	
   
	
}
