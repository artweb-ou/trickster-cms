<?php

class showSelectedDiscounts extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('column');
    }
}

