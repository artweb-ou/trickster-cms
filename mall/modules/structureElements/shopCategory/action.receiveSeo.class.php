<?php

class receiveSeoShopCategory extends structureElementAction
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
            'metaDescription',
            'canonicalUrl',
            'metaDenyIndex',
            'metaDescriptionTemplate',
            'metaTitleTemplate',
            'metaH1Template',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

