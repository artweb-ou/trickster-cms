<?php

class showCoopPaymentMethod extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('short');
    }
}