<?php

class showShoppingBasketStepDelivery extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasket.delivery.tpl');
    }
}

