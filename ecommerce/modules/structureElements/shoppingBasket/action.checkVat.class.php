<?php

class checkVatShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $vatNumber = $controller->getParameter('vatNumber');
        $shoppingBasket = $this->getService('shoppingBasket');
        $vatRate = $this->getService('ConfigManager')->get('main.vatRate');
        $structureElement->shoppingBasket = $shoppingBasket;
        if (!empty($vatNumber)) {
            $result = $structureElement->validateVatNumber($vatNumber);
            if($result['country_code'] != 'EE' && $result['valid']) {
//            if (true) {
                $vatRate = 1;
            }
            $shoppingBasket->setVatRate($vatRate);
            $shoppingBasket->recalculate();
            $renderer = $this->getService('renderer');
            $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
        }
    }
}