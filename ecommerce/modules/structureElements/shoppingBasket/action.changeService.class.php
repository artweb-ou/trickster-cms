<?php

class changeServiceShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $serviceId = $controller->getParameter('serviceId');
        $selected = filter_var($controller->getParameter('selected'), FILTER_VALIDATE_BOOLEAN);
        $shoppingBasket->setServiceSelection($serviceId, $selected);

        $renderer = $this->getService('renderer');
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
    }
}

