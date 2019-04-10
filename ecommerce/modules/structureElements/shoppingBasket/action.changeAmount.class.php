<?php

class changeAmountShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $responseStatus = "fail";
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $productAmount = $controller->getParameter('productAmount');
        $basketProductId = $controller->getParameter('basketProductId');

        if ($this->isAmountAvailable($productAmount, $basketProductId)) {
            $shoppingBasket->changeAmount($basketProductId, $productAmount);
            $responseStatus = "success";
        }
        $this->getService('renderer')->assign('responseStatus', $responseStatus);
        $renderer = $this->getService('renderer');
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
    }

    protected function isAmountAvailable($productAmount, $basketProductId)
    {
        $result = false;
        if (is_numeric($productAmount)) {
            $structureManager = $this->getService('structureManager');
            $shoppingBasket = $this->getService('shoppingBasket');

            $basketProductsIndex = $shoppingBasket->getProductsIndex();
            $productElementId = $basketProductsIndex[$basketProductId]->productId;
            $productElement = $structureManager->getElementById($productElementId);

            if ($productElement->availability == "quantity_dependent") {
                $quantityDiff = $productAmount - $basketProductsIndex[$basketProductId]->amount;
                if ($quantityDiff > 0) {
                    // product quantity is being increased, check availability
                    $finalQuantity = $shoppingBasket->getProductOverallQuantity($productElementId) + $quantityDiff;
                    if ($productElement->isPurchasable($finalQuantity)) {
                        $result = true;
                    }
                } else {
                    // quantity will decrease, no need for further checks
                    $result = true;
                }
            } elseif ($productElement->isPurchasable()) {
                $result = true;
            }
        }
        return $result;
    }
}

