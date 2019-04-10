<?php

class showPurchaseHistory extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $renderer = $this->getService('renderer');
        $renderer->assign("orders", $structureElement->getOrdersList());
        $structureElement->setTemplate('purchaseHistory.show.tpl');
    }
}

