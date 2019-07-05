<?php

class createOrder extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param orderElement $structureElement
     * @param string $payerLanguage
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->prepareActualData();

        $currencySelector = $this->getService('CurrencySelector');
        $currentCurrencyItem = $currencySelector->getDefaultCurrencyItem();
        $currentCurrencyName = $currentCurrencyItem->symbol;
        /**
         * @var shoppingBasket $shoppingBasket
         */
        $shoppingBasket = $this->getService('shoppingBasket');
        $formData = $shoppingBasket->getBasketFormData();

        $structureElement->orderNumber = $structureElement->countOrders();
        $structureElement->yearOrderNumber = $structureElement->countOrdersThisYear();

        $structureElement->invoiceNumber = $structureElement->generateOrderNumber('invoice_number_format');
        $structureElement->advancePaymentInvoiceNumber = $structureElement->generateOrderNumber('advance_invoice_number_number_format');
        $structureElement->orderConfirmationNumber = $structureElement->generateOrderNumber('confirmation_invoice_number_format');

        if (!($dueDays = $this->getService('settingsManager')->getSetting('order_duedate_days'))) {
            $dueDays = 5;
        }
        $structureElement->dueDate = strtotime($structureElement->dateCreated) + $dueDays * 24 * 60 * 60;

        if ($deliveryInfo = $shoppingBasket->getSelectedDeliveryType()) {
            $structureElement->deliveryPrice = $deliveryInfo->getPrice(false, false);
            $structureElement->deliveryType = $deliveryInfo->id;
            $structureElement->deliveryTitle = $deliveryInfo->title;
        }

        $structureElement->payerCompany = $formData['payerCompany'];
        $structureElement->payerFirstName = $formData['payerFirstName'];
        $structureElement->payerLastName = $formData['payerLastName'];
        $structureElement->payerEmail = $formData['payerEmail'];
        $structureElement->payerPhone = $formData['payerPhone'];
        $structureElement->payerAddress = $formData['payerAddress'];
        $structureElement->payerPostIndex = $formData['payerPostIndex'];
        $structureElement->payerCity = $formData['payerCity'];
        $structureElement->payerCountry = $formData['payerCountry'];
        $structureElement->currency = $currentCurrencyName;

        /**
         * @var languagesManager $languagesManager
         */
        $languagesManager = $this->getService('languagesManager');
        $structureElement->payerLanguage = $languagesManager->getCurrentLanguageCode();

        $structureElement->setOrderStatus('undefined');

        $structureElement->persistElementData();

        $addedProducts = $shoppingBasket->getProductsList();
        $structureElement->createOrderProducts($addedProducts);
        $structureElement->createOrderFields($shoppingBasket->getSelectedDeliveryType()->deliveryFormFields);
        $structureElement->createOrderDiscounts($shoppingBasket->getDiscountsList());

        foreach ($shoppingBasket->getSelectedServicesList() as $service) {
            if ($newOrderService = $structureManager->createElement('orderService', 'show', $structureElement->id)) {
                $newOrderService->prepareActualData();
                $newData = [];
                $newData['title'] = $service->title;
                $newData['serviceId'] = $service->id;
                $newData['price'] = $service->price;

                if ($newOrderService->importExternalData($newData)) {
                    $newOrderService->persistElementData();
                }
            }
        }
    }
}

