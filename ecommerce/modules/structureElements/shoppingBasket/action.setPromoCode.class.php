<?php

class setPromoCodeShoppingBasket extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param shoppingBasketElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        /**
         * @var shoppingBasket $shoppingBasket
         */
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $promoCode = $controller->getParameter('promoCode');
        $responseStatus = "fail";

        if ($shoppingBasket->setPromoCode($promoCode)) {
            $responseStatus = "success";
        }

        /**
         * @var jsonRendererPlugin $renderer
         */
        $renderer = $this->getService('renderer');
        $renderer->assign('responseStatus', $responseStatus);
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
    }
}