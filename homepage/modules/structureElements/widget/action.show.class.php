<?php

class showWidget extends structureElementAction
{
    /**
     * @param widgetElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('details');
    }
}

