<?php

class receiveBrandsWidget extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();

            $brandsElement = $structureManager->getElementByMarker('brands');
            $brandsList = $structureManager->getElementsFlatTree($brandsElement->id);

            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'brands', 'parent');

            foreach ($brandsList as &$brand) {
                if (isset($compiledLinks[$brand->id]) && !in_array($brand->id, $structureElement->brands)) {
                    $compiledLinks[$brand->id]->delete();
                } elseif (!isset($compiledLinks[$brand->id]) && in_array($brand->id, $structureElement->brands)) {
                    $linksManager->linkElements($structureElement->id, $brand->id, 'brands');
                }
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'displayMenus',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}