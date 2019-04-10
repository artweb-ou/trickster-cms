<?php

class receivePayment extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param paymentElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        /**
         * @var paymentsManager $paymentsManager
         */
        $paymentsManager = $this->getService('paymentsManager');

        if ($methodElement = $structureElement->getPaymentMethodElement()) {
            // not every payment method element has a method (such as invoice)
            /**
             * @var paymentsMethod $paymentMethod
             */
            if ($paymentMethod = $paymentsManager->getPaymentMethod($methodElement->getName())) {
                $paymentMethod->setAttributes($methodElement->getSpecialData($this->getService('languagesManager')
                    ->getCurrentLanguageId()));
                $pathsManager = $this->getService('PathsManager');
                $paymentMethod->setCertificatesPath($pathsManager->getPath('uploads'));
                $paymentMethod->loadExternalFiles();

                $paymentStatus = $paymentMethod->getTransactionResult();
                $payer = $paymentMethod->getPayerName();
                $account = $paymentMethod->getPayerAccount();
                $date = $paymentMethod->getPaymentDate();
                $amount = $paymentMethod->getPaymentAmount();
                $currency = $paymentMethod->getPaymentCurrency();

                $structureElement->payer = $payer;
                $structureElement->account = $account;
                $structureElement->date = $date;
                $structureElement->amount = $amount;
                $structureElement->currency = $currency;
                $structureElement->paymentStatus = $paymentStatus;

                $structureElement->persistElementData();
            }
        }
        $structureElement->updateOrderStatus();
    }
}
