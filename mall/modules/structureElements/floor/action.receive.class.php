<?php

class receiveFloor extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            if ($roomsMaps = $structureManager->getElementsByType('roomsMap')) {
                $linksManager = $this->getService('linksManager');
                foreach ($roomsMaps as &$roomsMap) {
                    $linksManager->linkElements($roomsMap->id, $structureElement->id, $roomsMap::LINK_TYPE_FLOOR);
                }
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'image',
        ];
    }
}


