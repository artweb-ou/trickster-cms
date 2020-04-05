<?php

class receiveLayoutFolder extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->persistElementData();
            $controller->redirect($structureElement->getUrl("showLayoutForm"));
        }
        $structureElement->executeAction("showLayoutForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'colorLayout',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


