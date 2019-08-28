<?php

class receiveLayoutDiscountsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
            $structureElement->executeAction("showLayoutForm");
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'layout',
            'productsLayout',
            'selectedDiscountProductsLayout',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}