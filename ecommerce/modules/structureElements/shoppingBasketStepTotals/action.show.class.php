<?php

class showShoppingBasketStepTotals extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasket.totals.tpl');
    }
}

