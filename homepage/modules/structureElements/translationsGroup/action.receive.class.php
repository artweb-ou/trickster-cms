<?php

class receiveTranslationsGroup extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;

            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
            $structureElement->setViewName('result');
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['title'];
    }
}

