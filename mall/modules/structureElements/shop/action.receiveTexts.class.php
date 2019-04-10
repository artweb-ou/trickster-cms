<?php

class receiveTextsShop extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction('showForm');
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'introduction',
            'content',
            'contactInfo',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

