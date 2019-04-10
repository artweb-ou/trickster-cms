<?php

class receivePollAnswer extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            //			$controller->redirect($structureElement->URL);
            //			$structureElement->setViewName('result');
            if ($parentElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->redirect($parentElement->URL);
            }
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'answerText',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['answerText'][] = 'notEmpty';
    }
}

