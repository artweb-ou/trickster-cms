<?php

class showPersonnelList extends structureElementAction
{
    /**
     * @param personnelListElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('content');
    }
}


