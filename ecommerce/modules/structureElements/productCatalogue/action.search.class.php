<?php

class searchProductCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('search');
    }
}

