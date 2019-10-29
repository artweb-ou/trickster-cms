<?php

class receiveLayoutLinkList extends structureElementAction
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
            'cols',
            'colWidthValue',
            'colWidthUnit',
            'gapValue',
            'gapUnit',
            'titlePosition',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}