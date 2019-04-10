<?php

class receiveUserGroup extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->groupName;
            $structureElement->persistElementData();
            $structureElement->setViewName('result');
            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setValidators(&$validators)
    {
        $validators['groupName'][] = 'notEmpty';
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'groupName',
            'description',
            'marker',
        ];
    }
}
