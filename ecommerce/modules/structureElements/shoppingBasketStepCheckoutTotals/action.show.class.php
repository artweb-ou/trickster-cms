<?php

class showShoppingBasketStepCheckoutTotals extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasket.checkout.tpl');
    }
}

