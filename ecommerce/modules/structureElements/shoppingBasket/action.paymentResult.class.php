<?php

class paymentResultShoppingBasket extends structureElementAction
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
        $bank = false;
        $status = 'undefined';
        if ($orderElement = $structureElement->getCurrentOrder()) {
            if ($paymentElement = $orderElement->getPaymentElement()) {
                $bank = $paymentElement->bank;
                $status = $paymentElement->paymentStatus;
            }
        }

        if ($status == 'success') {
            $shoppingBasket->clearShoppingBasket();
            $shoppingBasket->setMessage($structureElement->paymentSuccessfulText);
            $structureElement->setPaymentStatus(true);
        } elseif ($status == 'deferred') {
            $shoppingBasket->clearShoppingBasket();
            $shoppingBasket->setMessage($structureElement->paymentDeferredText);
        } elseif ($status == 'undefined' && ($bank == 'invoice' || $bank == 'query')) {
            $shoppingBasket->clearShoppingBasket();
            if ($bank == 'invoice') {
                $shoppingBasket->setMessage($structureElement->paymentInvoiceText);
                $structureElement->setPaymentStatus(true);
            } elseif ($bank == 'query') {
                $shoppingBasket->setMessage($structureElement->paymentQueryText);
            }
        } else {
            $structureElement->errorMessage = $structureElement->paymentFailedText;
        }
        $structureElement->executeAction('show');
        if ($structureElement->isPaymentStatus()) {
            $renderer = $this->getService('renderer');
            $renderer->assign('currentOrder', $orderElement);
        }
    }
}