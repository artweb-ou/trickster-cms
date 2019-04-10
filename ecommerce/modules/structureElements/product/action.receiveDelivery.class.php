<?php

class receiveDeliveryProduct extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->persistElementData();

            $structureElement->receiveDeliveryData();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'formDeliveries',
            'deliveryPriceType',
            'deliveryStatus',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

