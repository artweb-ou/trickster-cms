<?php

class showNewsList extends structureElementAction
{
    /**
     * @param newsListElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureElement->setViewName('details');
        }
    }
}