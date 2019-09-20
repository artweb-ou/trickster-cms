<?php

class receiveOrderProduct extends structureElementAction
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
            'title_dl',
            'price',
            'oldPrice',
            'productId',
            'description',
            'code',
            'amount',
            'unit',
            'variation_dl',
            'variation',
            'vatRate',
            'vatLessPrice',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}