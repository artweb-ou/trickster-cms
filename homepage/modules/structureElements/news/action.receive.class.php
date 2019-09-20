<?php

class receiveNews extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if (!$structureElement->structureName) {
                $structureElement->structureName = $structureElement->title;
            }

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $additionalImages = [
                'thumbImage',
            ];
            foreach($additionalImages as $imageCode) {
                if (!is_null($structureElement->getDataChunk($imageCode)->originalName)) {
                    $structureElement->$imageCode = $structureElement->id . "_$imageCode";
                    $field = $imageCode . 'OriginalName';
                    $structureElement->$field = $structureElement->getDataChunk($imageCode)->originalName;
                }
            }

            $structureElement->persistElementData();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'date',
            'title',
            'introduction',
            'content',
            'image',
            'thumbImage',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


