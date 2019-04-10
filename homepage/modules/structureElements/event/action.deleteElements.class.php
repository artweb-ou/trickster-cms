<?php

class deleteElementsEvent extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $elements = $structureElement->elements;
            foreach ($elements as $elementID => &$value) {
                if ($deletedElement = $structureManager->getElementById($elementID)) {
                    $deletedElement->groupDeletion = true;
                    $deletedElement->executeAction('delete');
                }
            }
        }
        $controller->restart();
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['elements'];
    }
}


