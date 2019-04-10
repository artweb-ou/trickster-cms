<?php

class receiveDiscountsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            $structureElement->persistElementData();

            // link discounts
            $discountsElement = $structureManager->getElementByMarker('discounts');
            $discountsList = $structureManager->getElementsFlatTree($discountsElement->id);

            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'discountsList', 'parent');

            foreach ($discountsList as &$discount) {
                if (isset($compiledLinks[$discount->id]) && !in_array($discount->id, $structureElement->discounts) && !$structureElement->connectAll
                ) {
                    $linksManager->unLinkElements($structureElement->id, $discount->id, 'discountsList');
                } elseif (!isset($compiledLinks[$discount->id]) && ($structureElement->connectAll || in_array($discount->id, $structureElement->discounts))
                ) {
                    $linksManager->linkElements($structureElement->id, $discount->id, 'discountsList');
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
            'discounts',
            'connectAll',
            'content',
            'columns',
            'hidden',
            'structureRole',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}
