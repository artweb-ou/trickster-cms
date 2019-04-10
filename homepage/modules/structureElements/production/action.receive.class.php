<?php

class receiveProduction extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');
            $structureElement->prepareActualData();

            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            if (!is_null($structureElement->getDataChunk("file")->originalName)) {
                $structureElement->file = $structureElement->id . '_doc';
                $structureElement->originalName2 = $structureElement->getDataChunk("file")->originalName;
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
            'file',
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


