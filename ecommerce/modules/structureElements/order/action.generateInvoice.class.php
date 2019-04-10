<?php

class generateInvoiceOrder extends structureElementAction
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
                    $structureElement->getPdfPath('orderConfirmation');
                }
                break;
            case 'advancePaymentInvoice':
                {
                    $structureElement->getPdfPath('advancePaymentInvoice');
                }
                break;
            case 'invoice':
                {
                    $structureElement->getPdfPath('invoice');
                }
                break;
        }

        $structureElement->executeAction("showForm");
    }
}

