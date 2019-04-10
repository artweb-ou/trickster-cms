<?php

class showShoppingBasketStatus extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shoppingBasketStatus.column.tpl');
    }
}

