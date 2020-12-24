<?php

class receiveOrder extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param orderElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            if ($fieldElements = $structureElement->getOrderFields()) {
                foreach ($fieldElements as $element) {
                    $element->executeAction('receive');
                }
            }
            if ($discountElements = $structureElement->getDiscountsList()) {
                foreach ($discountElements as $element) {
                    $element->executeAction('receive');
                }
            }
            if ($serviceElements = $structureElement->getServicesList()) {
                foreach ($serviceElements as $element) {
                    $element->executeAction('receive');
                }
            }

            $linksManager = $this->getService('linksManager');

            $structureElement->prepareActualData();
            if (!$structureElement->orderNumber) {
                $structureElement->orderNumber = $structureElement->countOrders();
            }

            $structureElement->invoiceNumber = $structureElement->generateOrderNumber('invoice_number_format');
            $structureElement->advancePaymentInvoiceNumber = $structureElement->generateOrderNumber('advance_invoice_number_number_format');
            $structureElement->orderConfirmationNumber = $structureElement->generateOrderNumber('confirmation_invoice_number_format');

            if (!$structureElement->yearOrderNumber) {
                $structureElement->yearOrderNumber = $structureElement->countOrdersThisYear();
            }
            $structureElement->persistElementData();
            $structureElement->checkInvoiceSending();

            if ($structureElement->userId) {
                $userOrderLinks = $linksManager->getElementsLinks($structureElement->id, 'userOrder', 'child');
                $alreadyLinked = false;
                foreach ($userOrderLinks as &$link) {
                    if ($link->parentStructureId != $structureElement->userId) {
                        $link->delete();
                    } else {
                        $alreadyLinked = true;
                    }
                }
                if (!$alreadyLinked) {
                    $linksManager->linkElements($structureElement->userId, $structureElement->id, 'userOrder');
                }
            }
            $controller->redirect($structureElement->getUrl('showForm'));
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'orderNumber',
            'dateCreated',
            'dueDate',
            'currency',
            'orderStatus',
            'deliveryType',
            'deliveryTitle',
            'deliveryPrice',
            'receiverCompany',
            'receiverFirstName',
            'receiverLastName',
            'receiverEmail',
            'receiverPhone',
            'receiverCity',
            'receiverAddress',
            'receiverPostIndex',
            'receiverCountry',
            'payerCompany',
            'payerFirstName',
            'payerLastName',
            'payerEmail',
            'payerPhone',
            'payerCity',
            'payerAddress',
            'payerPostIndex',
            'payerCountry',
            'userId',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

