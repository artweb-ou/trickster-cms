<?php

class showNewsMailsGroups extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final === true) {
        }
        $structureElement->setViewName('list');
    }
}