<?php

class receiveProductImportTemplate extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'importOrigin',
            'priceAdjustment',
            'delimiter',
            'ignoreFirstRow',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


