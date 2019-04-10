<?php

class showPositions extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param positionsElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('form');
    }
}
