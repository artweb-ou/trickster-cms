<?php

class receiveLayoutGallery extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->persistElementData();
            $controller->redirect($structureElement->getUrl('showLayoutForm'));
        }
        $structureElement->executeAction("showLayoutForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'layout',
            'colorLayout',
            'listLayout',
            'columns',
            'gapValue',
            'gapUnit',
            'clickDisable',
            'freeImageWidth',
            'captionLayout',
            'slideType',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}