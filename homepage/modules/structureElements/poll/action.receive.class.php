<?php

class receivePoll extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'description',
        ];
    }

    public function setValidators(&$validators)
    {
        //		$validators['title'][] = 'notEmpty';
        //		$validators['description'][] = 'notEmpty';
    }
}

