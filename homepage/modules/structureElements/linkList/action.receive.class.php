<?php

class receiveLinkList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            if ($structureElement->getDataChunk("image")->originalName) {
                $structureElement->image = $structureElement->id . '_1';
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $additionalImages = [
                'icon',
            ];

            foreach($additionalImages as $imageCode) {
                if (!is_null($structureElement->getDataChunk($imageCode)->originalName)) {
                    $structureElement->$imageCode = $structureElement->id . "_$imageCode";
                    $field = $imageCode . 'OriginalName';
                    $structureElement->$field = $structureElement->getDataChunk($imageCode)->originalName;
                }
            }

            $structureElement->persistElementData();

            $structureElement->persistDisplayMenusLinks();
            //$controller->redirect($structureElement->URL);
        }
        if ($controller->getApplicationName() != 'adminAjax') {
            $controller->redirect($structureElement->URL);
            //$structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'hideTitle',
            'image',
            'icon',
            'marker',
            'content',
            'subTitle',
            'displayMenus',
            'fixedId',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}