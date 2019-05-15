<?php

class showShoppingBasketStepAccount extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasket.account.tpl');
    }
}

