<?php

class saveCategoryLayoutCategory extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->persistElementData();
            $controller->redirect($structureElement->getUrl('showLayoutForm'));
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'layout',
            'categoryLayout',
            'productsMobileLayout',
            'categoriesMobileLayout',
            'colorLayout',
            'productsLayout',
            'collectionLayout'
        ];
    }

    public function setValidators(&$validators)
    {
    }
}