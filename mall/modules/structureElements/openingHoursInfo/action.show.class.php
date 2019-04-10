<?php

class showOpeningHoursInfo extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('openingHoursInfo.show.tpl');
    }
}