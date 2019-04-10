<?php

class archiveNewsList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $structureElement->setViewName('archive');
        }
    }
}

