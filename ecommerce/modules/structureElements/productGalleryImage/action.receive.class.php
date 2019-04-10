<?php

class receiveProductGalleryImage extends structureElementAction
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
            $structureElement->persistElementData();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'title',
            'description',
            'labelText',
            'link',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}