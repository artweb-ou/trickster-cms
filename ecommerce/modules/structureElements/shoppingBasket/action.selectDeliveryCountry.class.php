<?php

class selectDeliveryCountryShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $deliveryCountryId = $controller->getParameter('deliveryCountryId');
        $shoppingBasket->selectDeliveryCountry($deliveryCountryId);

        $renderer = $this->getService('renderer');
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
        $renderer->assign('responseStatus', "success");
    }
}

