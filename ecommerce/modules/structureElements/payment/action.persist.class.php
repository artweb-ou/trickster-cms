<?php

class persistPayment extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param paymentElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');

            $structureElement->prepareActualData();

            $structureElement->persistElementData();

            if ($payments = $structureManager->getElementByMarker('payments')) {
                $linksManager->linkElements($payments->id, $structureElement->id, 'structure');
            }
            $structureElement->updateOrderStatus();
            if ($orderElement = $structureElement->getOrderElement()) {
                $linksManager->unLinkElements($orderElement->id, $structureElement->id, 'structure');
                $linksManager->linkElements($orderElement->id, $structureElement->id, 'orderPayment');
            } else {
                $controller->restart($structureElement->URL);
            }
        }

        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'description',
            'orderId',
            'userId',
            'paymentStatus',
            'payer',
            'account',
            'date',
            'amount',
            'bank',
            'currency',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


