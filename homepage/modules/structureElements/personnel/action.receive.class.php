<?php

class receivePersonnel extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param personnelElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            if ($structureElement->getDataChunk("image")->originalName) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $structureElement->persistElementData();

            $structureElement->persistDisplayMenusLinks();
        }
        if ($controller->getApplicationName() != 'adminAjax') {
            $controller->redirect($structureElement->URL);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'title',
            'status',
            'phone',
            'mobilePhone',
            'email',
            'position',
            'content',
            'displayMenus',
            'link',
            'linkTitle',
        ];
    }
}