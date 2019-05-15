<?php

class showShoppingBasketStepDiscounts extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasket.discounts.tpl');
    }
}

