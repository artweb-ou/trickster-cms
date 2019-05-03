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
            case 'orderStatus.Notification.sent':
                {
                    $structureElement->sendOrderEmail('orderStatus.Notification.sent', true);
                }
                break;
        }
        $structureElement->executeAction("showForm");
    }
}