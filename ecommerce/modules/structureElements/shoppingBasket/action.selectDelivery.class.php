<?php

class selectDeliveryShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $deliveryId = $controller->getParameter('deliveryId');
        $shoppingBasket->selectDeliveryType($deliveryId);

        $renderer = $this->getService('renderer');
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
    }
}