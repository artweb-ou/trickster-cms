<?php

class showShop extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('short');
        if ($structureElement->roomId && $roomsIdList = array_keys($structureElement->roomId)) {
            $roomCode = reset($roomsIdList);
            if (strpos($roomCode, '_') !== false && $strings = explode('_', $roomCode)) {
                $structureElement->floor = $strings[0];
                $structureElement->number = $strings[1];
            }
        }

        $structureManager->getElementsChildren($structureElement->id);
        $structureElement->images = [];
        foreach ($structureElement->childrenList as &$childElement) {
            if ($childElement->structureType == 'galleryImage') {
                $structureElement->images[] = $childElement;
            }
        }
        if (count($structureElement->images)) {
            $structureElement->galleryImage = reset($structureElement->images);
        }
        $structureElement->contactInfo = str_ireplace("\n", "", $structureElement->contactInfo);
        $structureElement->contactInfo = str_ireplace("\r", "", $structureElement->contactInfo);

        if ($structureElement->requested) {
            $structureElement->setViewName('details');
            $structureElement->campaignsList = $structureManager->getElementsChildren($structureElement->id, null, 'campaigns');
        }
    }
}

