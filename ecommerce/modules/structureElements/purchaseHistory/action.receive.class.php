<?php

class receivePurchaseHistory extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->setViewName('form');
    }

    public function setValidators(&$validators)
    {
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
        ];
    }
}

