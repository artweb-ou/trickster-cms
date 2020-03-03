<?php

class receiveSeoProduct extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'structureName',
            'metaTitle',
            'h1',
            'metaDescription',
            'canonicalUrl',
            'metaDenyIndex',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

