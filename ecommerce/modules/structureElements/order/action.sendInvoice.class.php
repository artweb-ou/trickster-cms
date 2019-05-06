<?php

class sendInvoiceOrder extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param orderElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        switch ($controller->getParameter('invoiceType')) {
            case 'orderConfirmation':
                {
                    $structureElement->sendOrderEmail('orderConfirmation', true);
                }
                break;
            case 'advancePaymentInvoice':
                {
                    $structureElement->sendOrderEmail('advancePaymentInvoice', true);
                }
                break;
            case 'invoice':
                {
                    $structureElement->sendOrderEmail('invoice', true);
                }
                break;
            case 'Notification':
                {
                    $statusTypeNotification = $controller->getParameter('statusType');
                    $statusSendTrigger = !empty($controller->getParameter('sendTrigger'))?:false;
                    $structureElement->sendOrderStatusNotificationEmail('Notification', $statusTypeNotification, $statusSendTrigger,true);
                }
                break;
        }
        if ($controller->getParameter('invoiceType') === 'Notification' && $controller->getParameter('sendTrigger') === 'ajax'){
            $structureElement->executeAction("showForm");
        }
        else {
            $controller->redirect($structureElement->getUrl('showForm'));
        }
    }
}