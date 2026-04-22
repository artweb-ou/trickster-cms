<?php

class showNewsMailsGroups extends structureElementAction
{
    /**
     * @param newsMailsGroupsElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final === true) {
        }
        $structureElement->setViewName('list');
    }
}