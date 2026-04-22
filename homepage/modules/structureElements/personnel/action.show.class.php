<?php

class showPersonnel extends structureElementAction
{
    /**
     * @param personnelElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('short');
    }
}

