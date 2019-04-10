<?php

class showMap extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName($structureElement->getCurrentLayout());
    }
}

