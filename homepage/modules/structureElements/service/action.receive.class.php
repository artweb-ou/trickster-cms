<?php

class receiveService extends structureElementAction
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
            if (!is_null($structureElement->getDataChunk("icon")->originalName)) {
                $structureElement->icon = $structureElement->id . "_icon";
                $structureElement->iconOriginalName = $structureElement->getDataChunk("icon")->originalName;
            }

//            foreach($additionalImages as $imageKey=>$imageCode) {
//                if (!is_null($structureElement->getDataChunk($imageCode)->originalName)) {
//                    $structureElement->$imageCode = $structureElement->id . "_$imageKey";
//                    $field = $imageCode . 'OriginalName';
//                    $structureElement->$field = $structureElement->getDataChunk($imageCode)->originalName;
//                }
//            }

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
            $structureElement->persistDisplayMenusLinks();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'icon',
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
            'displayMenus',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
    }
}