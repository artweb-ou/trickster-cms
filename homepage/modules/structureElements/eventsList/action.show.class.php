<?php

class showEventsList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName("show");
        if ($structureElement->final && $structureElement->role !== 'container') {
            if ($parent = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->restart($parent->URL);
            }
        }
    }
}

