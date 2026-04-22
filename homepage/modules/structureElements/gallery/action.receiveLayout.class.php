<?php

class receiveLayoutGallery extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param galleryElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
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
            'listLayout',
            'columns',
            'captionLayout',
            'slideType',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}