<?php

class receiveProductVariation extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->persistElementData();

            $parentElement = $structureManager->getElementsFirstParent($structureElement->id);
            $controller->redirect($parentElement->URL);
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['title', 'color'];
    }
}


