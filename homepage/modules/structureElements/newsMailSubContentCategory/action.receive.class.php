<?php

class receiveNewsMailSubContentCategory extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param newsMailSubContentCategoryElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            $structureElement->persistDisplayMenusLinks();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->setViewName('form');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'code',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


