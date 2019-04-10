<?php

class showProductGallery extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->images = [];

        $childElements = $structureElement->getChildrenList();
        foreach ($childElements as &$childElement) {
            if ($childElement->structureType == 'productGalleryImage') {
                $structureElement->images[] = $childElement;
            }
        }
        if (count($structureElement->images)) {
            $firstImage = reset($structureElement->images);
            $structureElement->image = $firstImage->image;
            $structureElement->originalName = $firstImage->originalName;
        }

        $structureElement->setViewName('short');

        if ($structureElement->final) {
            $structureElement->setViewName('details');
        }
    }
}

