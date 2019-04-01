<?php
namespace VOPDebitConnect\Subscriber;
use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Order implements  SubscriberInterface
{
	public static function getSubscribedEvents() {
	
		return [
			'Enlight_Controller_Action_PostDispatch_Backend_Order' => 'onOrderPostDispatch',
			'Enlight_Controller_Action_PostDispatch_Backend_Customer' => 'onCustomerPostDispatch',
		];
	}
	
	 public function onOrderPostDispatch(\Enlight_Controller_ActionEventArgs $args)
	{
	try
	{
	
		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->getSubject();
		
		$view = $controller->View();
		$request = $controller->Request();
		$response = $args->getResponse();
		$orderId = $args->getRequest()->getParam('orderID');
		if($orderId>0)	$view->assign("pkOrder",$orderId);
		$view->addTemplateDir(__DIR__ . '/../Views/');
		if ($request->getActionName() == 'index') {
			$view->extendsTemplate('backend/v_o_p_debit_connect/orderapp.js');
		}
	
		if ($request->getActionName() == 'load') {
			$view->extendsTemplate('backend/v_o_p_debit_connect/view/detail/orderwindow.js');
		}
	}catch(Exception $e){
	
	}
	}
	 public function onCustomerPostDispatch(\Enlight_Controller_ActionEventArgs $args)
	{
	try
	{
	
		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->getSubject();
		
		$view = $controller->View();
		$request = $controller->Request();
		$response = $args->getResponse();
		$customerId = $args->getRequest()->getParam('customerID');
		if($customerId>0)	$view->assign("pkCustomer",$customerId);
		$view->addTemplateDir(__DIR__ . '/../Views/');
		if ($request->getActionName() == 'index') {
			$view->extendsTemplate('backend/v_o_p_debit_connect/customerapp.js');
		}
	
		if ($request->getActionName() == 'load') {
			$view->extendsTemplate('backend/v_o_p_debit_connect/view/detail/customerwindow.js');
		}
	}catch(Exception $e){
	
	}
	}
	
}