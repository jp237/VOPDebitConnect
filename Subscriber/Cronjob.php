<?php
namespace VOPDebitConnect\Subscriber;
use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \VOPDebitConnect\Components;

class Cronjob implements  SubscriberInterface
{
	public static function getSubscribedEvents() {
	
    return [
		'Shopware_CronJob_VOPCronjob' => 'CronJobAction',
		 'Enlight_Bootstrap_InitResource_vopdebitconnect.runcronjob' =>            'runCronjobService',
        'Enlight_Controller_Dispatcher_ControllerPath_Backend_VOPCronjob' => 'onGetControllerPathBackend',
   
    ];
}

	public function runCronjobService(\Enlight_Event_EventArgs $args){
	
		return new \VOPDebitConnect\Components\Cronjob();		
	}
	public function CronJobAction(\Enlight_Event_EventArgs $args)
	{
		// CREATE NEW SMARTY INSTANCE FOR SERVICE runcronjob	  
		$view = new \Enlight_View_Default(Shopware()->Container()->get('template'));
		$cronTask = Shopware()->Container()->get("vopdebitconnect.runcronjob");
	
		return $cronTask->getCronjobTask($view);
	}
	
	public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args) {

		return __DIR__ . '/../Controllers/Backend/VOPCronjob.php';
	}
	

}