<?php

class receiveLinkListItem extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            if ($structureElement->getDataChunk("image")->originalName) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $additionalImages = [
                'secondary'=>'secondaryImage',
                'tertiary'=>'tertiaryImage',
                'quaternary'=>'quaternaryImage',
            ];

//            if ($structureElement->getDataChunk("secondaryImage")->originalName) {
//                $structureElement->secondaryImage = $structureElement->id.'_secondary';
//                $structureElement->secondaryImageOriginalName = $structureElement->getDataChunk("secondaryImage")->originalName;
//            }

            foreach($additionalImages as $imageKey=>$imageCode) {
                if (!is_null($structureElement->getDataChunk($imageCode)->originalName)) {
                    $structureElement->$imageCode = $structureElement->id . "_$imageKey";
                    $field = $imageCode . 'OriginalName';
                    $structureElement->$field = $structureElement->getDataChunk($imageCode)->originalName;
                }
            }
            $structureElement->persistElementData();
        }
        if ($controller->getApplicationName() != 'adminAjax') {
            $controller->redirect($structureElement->URL);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'secondaryImage',
            'tertiaryImage',
            'quaternaryImage',
            'link',
            'linkText',
            'content',
            'title',
            'hideTitle',
            'fixedId',
            'marker',
            'highlighted',
            'colorLayout',
        ];
    }
}