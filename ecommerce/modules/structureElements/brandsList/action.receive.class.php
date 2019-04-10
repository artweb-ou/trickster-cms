<?php

class receiveBrandsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            $brandsElement = $structureManager->getElementByMarker('brands');
            $brandsList = $structureManager->getElementsChildren($brandsElement->id);

            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'brands', 'parent');

            foreach ($brandsList as &$brand) {
                if (isset($compiledLinks[$brand->id]) && !in_array($brand->id, $structureElement->brands) && !$structureElement->connectAll
                ) {
                    $compiledLinks[$brand->id]->delete();
                } elseif (!isset($compiledLinks[$brand->id]) && ($structureElement->connectAll || in_array($brand->id, $structureElement->brands))
                ) {
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
            'brands',
            'columns',
            'content',
            'connectAll',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


