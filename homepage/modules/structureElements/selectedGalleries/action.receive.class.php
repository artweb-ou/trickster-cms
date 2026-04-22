<?php

class receiveSelectedGalleries extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param selectedGalleriesElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $linksManager = $this->getService(linksManager::class);

            //persist gallery data
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;

            $structureElement->persistElementData();

            //persist connected galleries
            $compiledLinks = [];
            if ($connectedGalleryLinks = $linksManager->getElementsLinks($structureElement->id, 'selectedGalleries')) {
                foreach ($connectedGalleryLinks as &$link) {
                    $galleryId = $link->childStructureId;
                    $compiledLinks[$galleryId] = $link;
                }
            }
            $marker = $this->getService(ConfigManager::class)->get('main.rootMarkerPublic');
            $publicRoot = $structureManager->getElementByMarker($marker);
            $languages = $structureManager->getElementsChildren($publicRoot->id);
            $currentLanguageId = false;
            foreach ($languages as &$languageElement) {
                if ($languageElement->requested) {
                    $currentLanguageId = $languageElement->id;
                }
            }

            $galleriesList = [];
            if ($currentLanguageId) {
                $galleriesList = $structureManager->getElementsByType('gallery', $currentLanguageId);
            }

            foreach ($galleriesList as &$gallery) {
                if (isset($compiledLinks[$gallery->id]) && !in_array($gallery->id, $structureElement->galleries)) {
                    $linksManager->unLinkElements($structureElement->id, $gallery->id, 'selectedGalleries');
                } elseif (!isset($compiledLinks[$gallery->id]) && in_array($gallery->id, $structureElement->galleries)
                ) {
                    $linksManager->linkElements($structureElement->id, $gallery->id, 'selectedGalleries', true);
                }
            }

            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'content',
            'galleries',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

