<?php

class showEvent extends structureElementAction
{
    /**
     * @param eventElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if (!$structureElement->final) {
            $structureElement->setViewName('short');
        } else {
            $structureElement->setViewName('details');
        }
    }
}

