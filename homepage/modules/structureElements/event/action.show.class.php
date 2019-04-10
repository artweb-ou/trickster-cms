<?php

class showEvent extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if (!$structureElement->final) {
            $structureElement->setViewName('short');
        } else {
            $structureElement->setViewName('details');
        }
    }
}

