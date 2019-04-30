<?php

class showShoppingBasketStepPayments extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasket.paymentmethods.tpl');
    }
}

