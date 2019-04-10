<?php

class showDiscount extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('content');

        if ($structureElement->requested) {
            $structureElement->productsList = null;
            $structureElement->loadDiscountsListFilterData();
            $renderer = $this->getService('renderer');
            $renderer->assign('productsLayout', $structureElement->getDiscountsListProductsLayout());

            $structureElement->setViewName('details');
        }
    }
}