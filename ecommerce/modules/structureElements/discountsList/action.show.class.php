<?php

class showDiscountsList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('discountsList.content.tpl');
    }
}