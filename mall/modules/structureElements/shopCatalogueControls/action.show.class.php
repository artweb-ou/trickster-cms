<?php

class showShopCatalogueControls extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shopCatalogueControls.show.tpl');
    }
}

