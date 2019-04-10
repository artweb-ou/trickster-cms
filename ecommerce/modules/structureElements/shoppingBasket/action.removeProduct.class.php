<?php

class removeProductShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $basketProductId = $controller->getParameter('basketProductId');
        $shoppingBasket->removeProduct($basketProductId);

        $renderer = $this->getService('renderer');
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
        $renderer->assign('responseStatus', "success");
    }
}