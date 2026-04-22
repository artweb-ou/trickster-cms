<?php

class showFormSelectedGalleries extends structureElementAction
{
    /**
     * @param selectedGalleriesElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $linksManager = $this->getService(linksManager::class);
        if ($structureElement->requested) {
            $compiledLinks = [];
            if ($connectedGalleryLinks = $linksManager->getElementsLinks($structureElement->id, 'selectedGalleries', 'parent')
            ) {
                foreach ($connectedGalleryLinks as &$link) {
                    $galleryId = $link->childStructureId;
                    $compiledLinks[$galleryId] = $link;
                }
            }

            $structureElement->galleriesInfo = [];
            $marker = $this->getService(ConfigManager::class)->get('main.rootMarkerPublic');
            $publicRoot = $structureManager->getElementByMarker($marker);
            $languages = $structureManager->getElementsChildren($publicRoot->id);
            $currentLanguageId = false;
            foreach ($languages as &$languageElement) {
                if ($languageElement->requested) {
                    $currentLanguageId = $languageElement->id;
                }
            }

            if ($currentLanguageId) {
                $galleriesList = $structureManager->getElementsByType('gallery', $currentLanguageId);
                foreach ($galleriesList as &$gallery) {
                    if ($gallery->id != $structureElement->id) {
                        $galleryInfo = [];
                        $galleryInfo['title'] = $gallery->id;
                        $galleryInfo['select'] = isset($compiledLinks[$gallery->id]);
                        $galleryInfo['title'] = $gallery->getTitle();
                        $galleryInfo['id'] = $gallery->id;
                        $structureElement->galleriesInfo[] = $galleryInfo;
                    }
                }
            }
            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService(renderer::class);
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}