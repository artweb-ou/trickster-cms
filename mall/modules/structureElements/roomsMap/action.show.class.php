<?php

class showRoomsMap extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $structureElement->setViewName('content');
            $structureElement->categoriesList = $structureElement->getCategories();
        }
    }
}