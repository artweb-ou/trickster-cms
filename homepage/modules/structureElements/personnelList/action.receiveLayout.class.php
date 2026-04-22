<?php

class receiveLayoutPersonnelList extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param personnelListElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'layout',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


