<?php

class showCollection extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('content');
        if ($structureElement->requested) {
            $structureElement->setViewName('details');
        }
    }
}

