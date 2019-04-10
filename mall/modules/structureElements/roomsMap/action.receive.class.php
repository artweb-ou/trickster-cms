<?php

class receiveRoomsmap extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            $floors = [];
            $floorsElement = $structureManager->getElementByMarker('floors');
            if ($floorsElement) {
                $floors = $structureManager->getElementsChildren($floorsElement->id);
            }
            if ($floors) {
                $linksManager = $this->getService('linksManager');
                $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, $structureElement::LINK_TYPE_FLOOR, 'parent');
                foreach ($floors as &$floor) {
                    if (!isset($compiledLinks[$floor->id])) {
                        $linksManager->linkElements($structureElement->id, $floor->id, $structureElement::LINK_TYPE_FLOOR);
                    }
                }
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
            $structureElement->viewName = 'result';
        } else {
            $structureElement->viewName = 'form';
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'content',
        ];
    }
}

