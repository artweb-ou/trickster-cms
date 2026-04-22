<?php

class receivePollQuestion extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param pollQuestionElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
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
            'questionText',
            'multiChoice',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['questionText'][] = 'notEmpty';
    }
}

