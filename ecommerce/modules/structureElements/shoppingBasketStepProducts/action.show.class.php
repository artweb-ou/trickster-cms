<?php

class showShoppingBasketStepProducts extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasket.products.tpl');
    }
}

