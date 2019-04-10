<?php

class showFloorPlanControls extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('floorPlanControls.show.tpl');
    }
}