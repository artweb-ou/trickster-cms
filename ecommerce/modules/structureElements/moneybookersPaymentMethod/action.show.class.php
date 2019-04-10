<?php

class showMoneybookersPaymentMethod extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('short');
    }
}