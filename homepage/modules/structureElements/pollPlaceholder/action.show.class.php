<?php

class showPollPlaceholder extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('pollPlaceholder.column.tpl');
    }
}

