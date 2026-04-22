<?php

class receiveService extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param serviceElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $linksManager = $this->getService(linksManager::class);
            $structureElement->prepareActualData();

            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $structureElement->persistElementData();

            $linksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'connectedGallery', 'parent');
            foreach ($structureElement->galleries as &$galleryId) {
                if (is_numeric($galleryId)) {
                    $linksManager->linkElements($structureElement->id, $galleryId, 'connectedGallery', true);
                }
                unset($linksIndex[$galleryId]);
            }
            foreach ($linksIndex as &$link) {
                $link->delete();
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'image',
            'link_1',
            'link_2',
            'link_text_1',
            'link_text_2',
            'introduction',
            'content',
            'galleries',
            'feedbackId',
            'structureRole',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
    }
}