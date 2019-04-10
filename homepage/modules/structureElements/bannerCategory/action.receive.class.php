<?php

class receiveBannerCategory extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->limit = (int)$structureElement->limit;

            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'limit',
            'displayMenus',
            'marker',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}