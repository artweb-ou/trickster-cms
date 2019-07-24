<?php

class receiveCategory extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $destinationUrl = $structureElement->URL;

            $structureElement->prepareActualData();

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }

            $structureElement->persistElementData();

            $controller->redirect($destinationUrl);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'hidden',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}