<?php

class receiveWidget extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $additionalImages = [
                2=>'image2',
            ];
            foreach($additionalImages as $imageKey=>$imageCode) {
                if (!is_null($structureElement->getDataChunk($imageCode)->originalName)) {
                    $structureElement->$imageCode = $structureElement->id . "_$imageKey";
                    $field = $imageCode . 'OriginalName';
                    $structureElement->$field = $structureElement->getDataChunk($imageCode)->originalName;
                }
            }
            $structureElement->persistElementData();

            $structureElement->persistDisplayMenusLinks();

            $controller->redirect($structureElement->URL);
        }

        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'structureName',
            'title',
            'hideTitle',
            'content',
            'code',
            'image',
            'image2',
            'displayMenus',
            'marker',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}