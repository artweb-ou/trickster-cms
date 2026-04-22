<?php

class showGalleryImage extends structureElementAction
{
    /**
     * @param galleryImageElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('details');
    }
}