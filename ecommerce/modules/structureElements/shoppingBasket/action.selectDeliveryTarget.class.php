<?php

class selectDeliveryTargetShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $deliveryTargetId = $controller->getParameter('deliveryTargetId');
        $shoppingBasket->selectDeliveryCity($deliveryTargetId);

        $renderer = $this->getService('renderer');
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
        $renderer->assign('responseStatus', "success");
    }
}

