<?php
namespace VOPDebitConnect\Subscriber;
use Enlight\Event\SubscriberInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Backend implements  SubscriberInterface
{

    public static function getSubscribedEvents() {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_VOPDebitConnect' => 'onGetControllerPathBackend',
        ];
    }

	public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args) {
        return __DIR__ . '/../Controllers/Backend/VOPDebitConnect.php';
	}

}