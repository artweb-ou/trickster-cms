<?php

class checkVatShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $renderer = $this->getService('renderer');

        /**
         * @var $structureElement shoppingBasketElement
         */
        $vatNumber = $controller->getParameter('vatNumber');
        $shoppingBasket = $this->getService('shoppingBasket');
        $vatRate = $this->getService('ConfigManager')->get('main.vatRate');
        $vatCheckEnable = $this->getService('ConfigManager')->get('main.vatCheckEnable');
        $vatCheckCurrentCountry = $this->getService('ConfigManager')->get('main.vatCheckCurrentCountry');
        $structureElement->shoppingBasket = $shoppingBasket;
        $responseStatus = 'fail';
        if ($vatCheckEnable) {
            if (!empty($vatNumber)) {
                $result = $structureElement->validateVatNumber($vatNumber);
                if ($result['valid']) {
                    $responseStatus = 'success';
                    if ($result['country_code'] != $vatCheckCurrentCountry) {
                        $vatRate = 1;
                    }
                }
                $shoppingBasket->setVatRate($vatRate);
                $shoppingBasket->recalculate();
                $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
            }
        }
        $renderer->assign('responseStatus', $responseStatus);
    }
}