<?php

class showSelectedGalleries extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');
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

