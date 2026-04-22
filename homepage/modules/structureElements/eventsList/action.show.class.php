<?php

class showEventsList extends structureElementAction
{
    /**
     * @param eventsListElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName("show");
        if ($structureElement->final && $structureElement->structureRole !== 'container') {
            if ($parent = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->restart($parent->URL);
            }
        }
    }
}
