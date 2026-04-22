<?php

class showMap extends structureElementAction
{
    /**
     * @param mapElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName($structureElement->getCurrentLayout());
    }
}

