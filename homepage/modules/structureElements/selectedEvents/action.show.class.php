<?php

class showSelectedEvents extends structureElementAction
{
    /**
     * @param selectedEventsElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setTemplate('selectedEvents.content.tpl');
    }
}

