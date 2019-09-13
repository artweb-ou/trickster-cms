<?php

class receiveSortingFilterCategory extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'defaultOrder',
            'manualSortingEnabled',
            'amountOnPageEnabled',
            'priceSortingEnabled',
            'nameSortingEnabled',
            'dateSortingEnabled',
            'brandFilterEnabled',
            'parameterFilterEnabled',
            'discountFilterEnabled',
            'availabilityFilterEnabled',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

