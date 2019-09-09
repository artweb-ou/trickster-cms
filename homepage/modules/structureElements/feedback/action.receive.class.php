<?php

class receiveFeedback extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;

            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();
            $controller->redirect($structureElement->URL);
            $structureElement->setViewName('result');
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'hideTitle',
            'destination',
            'content',
            'buttonTitle',
            'role',
            'displayMenus'
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['destination'][] = 'email';
    }
}


