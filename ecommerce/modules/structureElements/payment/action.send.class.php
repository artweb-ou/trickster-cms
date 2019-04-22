<?php

class sendPayment extends structureElementAction
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
        $languagesManager = $this->getService('languagesManager');
        $currentLanguageElementId = $languagesManager->getCurrentLanguageId();

        $currencySelector = $this->getService('CurrencySelector');
        $currentCurrency = $currencySelector->getSelectedCurrencyCode();

        /** @var paymentsManager $paymentsManager */
        $paymentsManager = $this->getService('paymentsManager');
        $methodElement = $structureElement->getPaymentMethodElement();

        if ($methodElement && $method = $paymentsManager->getPaymentMethod($methodElement->getName())) {
            $method->setAttributes($methodElement->getSpecialData($currentLanguageElementId));
            $pathsManager = $this->getService('PathsManager');
            $method->setCertificatesPath($pathsManager->getPath('uploads'));
            $method->loadExternalFiles();

            $method->setTransactionCode($structureElement->id);
            $method->setCurrencyName($currentCurrency);
            $returnUrl = $controller->baseURL . 'banklink/pid:' . $methodElement->id . '/';
            if (substr($returnUrl, 0, 2) === '//') {
                $protocolPrefix = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
                $returnUrl = $protocolPrefix . substr($returnUrl, 2);
            }
            $method->setReturnURL($returnUrl);
            $method->setPaymentAmount($structureElement->amount);
            $method->setLanguageCode($languagesManager->getCurrentLanguageCode());
            $method->setReferenceNumber('');
            if ($orderElement = $structureElement->getOrderElement()) {
                $method->setExplanationText('Order nr: ' . $orderElement->getInvoiceNumber('advancePaymentInvoice'));

                if ($method instanceof OrderDataPaymentMethodInterface) {
                    $method->setOrderData($orderElement->getOrderData());
                }
            }
            $method->setPayerEmail($orderElement->payerEmail);
            if ($transactionData = $method->getTransactionData()) {
                $requestType = $method->getRequestType();

                $logRecord = new BankLogRecordInfo();
                $logDetails = [];
                $logDetails['type'] = strtoupper($requestType);
                $logDetails['transactionData'] = $transactionData;
                $logRecord->setPaymentId($structureElement->id);
                $logRecord->setTime($_SERVER['REQUEST_TIME']);
                $logRecord->setDetails($logDetails);
                $logRecord->setFromBank(false);
                $bankLog = $this->getService('bankLog');
                $bankLog->saveRecord($logRecord);

                if ($requestType == 'get') {
                    $controller->redirect($transactionData);
                } elseif ($requestType == 'post') {
                    $renderer = $this->getService('renderer');

                    $designThemesManager = $this->getService('DesignThemesManager');
                    $theme = $designThemesManager->getCurrentTheme();

                    $renderer->assign('formData', $transactionData);
                    $renderer->setTemplate($theme->template('payment.postredirect.tpl'));
                    $renderer->setCacheControl('no-cache');
                    $renderer->setContentType('text/html');
                    $renderer->display();
                    exit;
                } elseif ($requestType == 'internal') {
                    exit;
                }
            }
        }
    }
}