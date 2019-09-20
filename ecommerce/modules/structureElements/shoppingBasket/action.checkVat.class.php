<?php

class checkVatShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        /**
         * @var $structureElement shoppingBasketElement
         */
        $vatNumber = $controller->getParameter('vatNumber');
        $shoppingBasket = $this->getService('shoppingBasket');
        $vatRate = $this->getService('ConfigManager')->get('main.vatRate');
        $vatCheckeEneble = $this->getService('ConfigManager')->get('main.vatCheckEnable');
        $vatCheckCurrentCountry = $this->getService('ConfigManager')->get('main.vatCheckCurrentCountry');
        $structureElement->shoppingBasket = $shoppingBasket;
        if($vatCheckeEneble) {
            if (!empty($vatNumber)) {
                $result = $structureElement->validateVatNumber($vatNumber);
                if($result['valid'] && $result['country_code'] != $vatCheckCurrentCountry) {
                    $vatRate = 1;
                }
                $shoppingBasket->setVatRate($vatRate);
                $shoppingBasket->recalculate();
                $renderer = $this->getService('renderer');
                $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());

            }
        }
    }
}