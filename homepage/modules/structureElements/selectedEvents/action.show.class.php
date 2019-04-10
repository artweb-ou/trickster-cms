<?php

class showSelectedEvents extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('eventsList.show.tpl');
    }
}

