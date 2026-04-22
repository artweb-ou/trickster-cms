<?php

class archiveNewsList extends structureElementAction
{
    /**
     * @param newsListElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->requested) {
            $structureElement->setViewName('archive');
        }
    }
}

