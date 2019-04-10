<?php

class performShopCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->executeAction('show');
        if ($this->validated === true) {
            $structureElement->phrase = trim($structureElement->phrase);
            $structureElement->foundElements = $structureElement->performSearch($structureElement->phrase);
        } else {
            $structureElement->foundElements = [];
        }
        $structureElement->setViewName('search');
    }

    public function setValidators(&$validators)
    {
        $validators['phrase'][] = 'notEmpty';
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['phrase'];
    }
}

