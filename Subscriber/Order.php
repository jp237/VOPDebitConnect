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

class Order implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Backend_Order' => 'onOrderPostDispatch',
            'Enlight_Controller_Action_PostDispatch_Backend_Customer' => 'onCustomerPostDispatch',
        ];
    }

    public function onOrderPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        try {
            /** @var \Enlight_Controller_Action $controller */
            $controller = $args->getSubject();

            $view = $controller->View();
            $request = $controller->Request();
            $response = $args->getResponse();
            $orderId = $args->getRequest()->getParam('orderID');
            if ($orderId > 0) {
                $view->assign('pkOrder', $orderId);
            }
            $view->addTemplateDir(__DIR__ . '/../Views/');
            if ($request->getActionName() == 'index') {
                $view->extendsTemplate('backend/v_o_p_debit_connect/orderapp.js');
            }

            if ($request->getActionName() == 'load') {
                $view->extendsTemplate('backend/v_o_p_debit_connect/view/detail/orderwindow.js');
            }
        } catch (Exception $e) {
        }
    }

    public function onCustomerPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        try {
            /** @var \Enlight_Controller_Action $controller */
            $controller = $args->getSubject();

            $view = $controller->View();
            $request = $controller->Request();
            $response = $args->getResponse();
            $customerId = $args->getRequest()->getParam('customerID');
            if ($customerId > 0) {
                $view->assign('pkCustomer', $customerId);
            }
            $view->addTemplateDir(__DIR__ . '/../Views/');
            if ($request->getActionName() == 'index') {
                $view->extendsTemplate('backend/v_o_p_debit_connect/customerapp.js');
            }

            if ($request->getActionName() == 'load') {
                $view->extendsTemplate('backend/v_o_p_debit_connect/view/detail/customerwindow.js');
            }
        } catch (Exception $e) {
        }
    }
}
