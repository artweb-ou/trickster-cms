<?php

class showSelectedGalleries extends structureElementAction
{
    /**
     * @param selectedGalleriesElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $linksManager = $this->getService(linksManager::class);
        $structureElement->setViewName('show');

        $structureElement->galleriesList = [];
        if ($connectedGalleryLinks = $linksManager->getElementsLinks($structureElement->id, 'selectedGalleries', 'parent')
        ) {
            foreach ($connectedGalleryLinks as &$link) {
                if ($galleryElement = $structureManager->getElementById($link->childStructureId)) {
                    $structureElement->galleriesList[] = $galleryElement;
                }
            }
        }
    }
}

