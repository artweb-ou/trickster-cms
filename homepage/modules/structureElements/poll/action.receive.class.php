<?php

class receivePoll extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param pollElement $structureElement
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

