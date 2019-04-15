<?php

class receiveProductGallery extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            if (!is_null($structureElement->getDataChunk('image')->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk('image')->originalName;
            }
            if (!is_null($structureElement->getDataChunk('icon')->originalName)) {
                $structureElement->icon = $structureElement->id . 'ico';
                $structureElement->iconOriginalName = $structureElement->getDataChunk('icon')->originalName;
            }
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'description',
            'structureRole',
            'displayMenus',
            'popup',
            'showConnectedProducts',
            'layout',
            'markerLogic'
        ];
    }

    public function setValidators(&$validators)
    {
    }
}