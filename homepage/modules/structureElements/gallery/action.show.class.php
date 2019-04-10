<?php

class showGallery extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('short');
        $structureManager->getElementsChildren($structureElement->id);

        $structureElement->images = [];

        $childElements = $structureElement->getChildrenList();
        foreach ($childElements as &$childElement) {
            if ($childElement->structureType == 'galleryImage') {
                $structureElement->images[] = $childElement;
            }
        }
        if (count($structureElement->images)) {
            $firstImage = reset($structureElement->images);
            $structureElement->image = $firstImage->image;
            $structureElement->originalName = $firstImage->originalName;
        }

        if ($structureElement->requested || $structureElement->getCurrentLayout('listLayout') == 'details') {
            $structureElement->setViewName($structureElement->getCurrentLayout('layout'));
        } else {
            $structureElement->setViewName($structureElement->getCurrentLayout('listLayout'));
        }
    }
}