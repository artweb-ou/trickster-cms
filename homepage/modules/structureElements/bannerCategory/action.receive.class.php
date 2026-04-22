<?php

class receiveBannerCategory extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param bannerCategoryElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
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