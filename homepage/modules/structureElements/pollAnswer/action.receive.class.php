<?php

class receivePollAnswer extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param pollAnswerElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
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

