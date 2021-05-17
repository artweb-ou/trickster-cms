<?php

class banklinkApplication extends controllerApplication
{
    protected $applicationName = 'banklink';
    public $rendererName = 'smarty';

    public function initialize()
    {
        $configManager = $this->getService('ConfigManager');
        $this->startSession('public', $configManager->get('main.publicSessionLifeTime'));
        $this->createRenderer();
    }

    public function execute($controller)
    {
//        $_GET = $_POST = $_REQUEST = json_decode('', true);

        /**
         * @var structureManager $structureManager
         */
        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->rootURL,
            'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
        ], true);
        $structureManager->getCurrentElement();
        /**
         * @var paymentsManager $paymentsManager
         */
        $paymentsManager = $this->getService('paymentsManager');
        $paymentMethodElementId = $controller->getParameter('pid');
        $paymentMethod = false;
        $processingResult = false;
        $transactionCode = null;
        $transactionResult = null;
        if ($paymentMethodElementId && $paymentMethodElement = $structureManager->getElementById($paymentMethodElementId, null, true)
        ) {
            if ($paymentMethod = $paymentsManager->getPaymentMethod($paymentMethodElement->getName())) {
                $paymentMethod->setAttributes($paymentMethodElement->getSpecialData($this->getService('LanguagesManager')
                    ->getCurrentLanguageId()));
                $pathsManager = $this->getService('PathsManager');
                $paymentMethod->setCertificatesPath($pathsManager->getPath('uploads'));
                $paymentMethod->loadExternalFiles();

                $transactionResult = $paymentMethod->getTransactionResult();
                $transactionCode = $paymentMethod->getTransactionCode();

                if ($transactionCode) {
                    $logRecord = new BankLogRecordInfo();
                    $logDetails = [];
                    $logDetails['result'] = $transactionResult;
                    $logDetails['type'] = strtoupper($_SERVER['REQUEST_METHOD']);
                    $url = $_SERVER['REQUEST_URI'];
                    if (isset($_SERVER['HTTP_HOST'])) {
                        $url = $_SERVER['HTTP_HOST'] . $url;
                        $scheme = !empty($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) !== 'OFF'
                            ? 'https' : 'http';
                        $url = $scheme . '://' . $url;
                    }
                    $logDetails['url'] = $url;
                    if (strtoupper($logDetails['type']) === 'POST') {
                        $logDetails['post data'] = json_encode($_POST);
                    }
                    if (isset($_SERVER['HTTP_REFERER'])) {
                        $logDetails['referer'] = $_SERVER['HTTP_REFERER'];
                    }
                    $logRecord->setPaymentId($transactionCode);
                    $logRecord->setTime($_SERVER['REQUEST_TIME']);
                    $logRecord->setDetails($logDetails);
                    $logRecord->setFromBank(true);
                    /**
                     * @var bankLog $bankLog
                     */
                    $bankLog = $this->getService('bankLog');
                    $bankLog->saveRecord($logRecord);
                }
                if ($paymentElement = $structureManager->getElementById($transactionCode, null, true)) {
                    $paymentElement->executeAction('receive');
                    $processingResult = true;
                }
            }
        }

        if ($paymentMethod && $controller->getParameter('response') && $paymentMethod instanceof TransactionResponsePaymentMethodInterface) {
            echo $paymentMethod->getTransactionResponse($processingResult);
        } else {
            $user = $this->getService('user');
            if ($URL = $user->getStorageAttribute('banklinkResultURL')) {
                $controller->redirect($URL);
            } else {
                print_r($_SESSION);
                echo 'banklink result url is missing. transaction id:' . $transactionCode;
                $this->logError('banklink result url is missing. transaction id:' . $transactionCode);
            }
        }
    }
}