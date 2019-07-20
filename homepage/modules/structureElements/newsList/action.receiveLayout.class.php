<?php

class receiveLayoutNewsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $images = [
                'generalOwnerAvatar',
                'socMedia_1_Icon',
            ];
            foreach($images as $imageCode) {
                if (!is_null($structureElement->getDataChunk($imageCode)->originalName)) {
                    $structureElement->$imageCode = $structureElement->id . "_$imageCode";
                    $field = $imageCode . 'OriginalName';
                    $structureElement->$field = $structureElement->getDataChunk($imageCode)->originalName;
                }
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->getUrl('showLayoutForm'));
        }
        $structureElement->executeAction("showLayoutForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'layout',
            'cols',
            'captionLayout',
            'generalOwnerName',
            'generalOwnerAvatar',
            'socMedia_1_Name',
            'socMedia_1_Icon',
//            'socMedia_1_Link',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}