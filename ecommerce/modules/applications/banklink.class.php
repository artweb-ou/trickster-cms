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
        //                $_POST = unserialize('a:27:{s:10:"VK_SERVICE";s:4:"1911";s:10:"VK_VERSION";s:3:"008";s:9:"VK_SND_ID";s:3:"EYP";s:9:"VK_REC_ID";s:8:"KVARTSTR";s:8:"VK_STAMP";s:4:"6509";s:6:"VK_REF";s:0:"";s:6:"VK_MSG";s:19:"Order nr: adv-00095";s:6:"VK_MAC";s:172:"V5/uvmE4q3hKAzS08Fw3OAFO0nPKf5JvFNe1jKvV4MSkywWphEggavaDg2y7cuSPkGbxcvvOJEXSQ3VgVALJHd2YW0Uv1b5XByyypNKAVGh5tmlzYqMLvoIGTT/8hxbPmqt6A/j9xBZ7zUamFq0PycDDy+52XBKaYhwVBdZMXUI=";s:7:"VK_LANG";s:3:"EST";s:9:"VK_CANCEL";s:40:"http://blackpepper.ee/banklink/pid:5172/";s:7:"VK_AUTO";s:1:"N";s:11:"VK_ENCODING";s:5:"UTF-8";s:4:"keel";s:3:"EST";s:7:"appname";s:13:"INTERNETIPANK";s:12:"SubmitButton";s:22:"Tagasi kaupmehe juurde";s:3:"act";s:9:"UPOSTEST2";s:7:"sesskey";s:32:"cilkvbdQafjkcGkirdblcdglcBqjtjTB";s:5:"frnam";s:1:"X";s:12:"unetmenuhigh";s:0:"";s:11:"unetmenulow";s:0:"";s:14:"unetmenulowdiv";s:0:"";s:4:"lang";s:3:"EST";s:6:"public";s:32:"17ec5798e6584dcb03c474242b059e68";s:29:"currentLanguageadminLanguages";s:3:"est";s:26:"currentLanguagepublic_root";s:3:"est";s:3:"_ga";s:26:"GA1.2.422861126.1469612176";s:4:"_gat";s:1:"1";}');
        //                foreach ($_POST as $key => &$value) {
        //                    $_REQUEST[$key] = $value;
        //                }
        $logFilePath = $this->getService('PathsManager')->getPath('logs') . 'banklog.txt';
        $postcontents = serialize($_REQUEST) . "\n";
        file_put_contents($logFilePath, $postcontents, FILE_APPEND);

        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->rootURL,
            'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
        ], true);
        $basketElement = $structureManager->getCurrentElement();
        /**
         * @var paymentsManager $paymentsManager
         */
        $paymentsManager = $this->getService('paymentsManager');
        $paymentMethodElementId = $controller->getParameter('pid');
        $paymentMethod = false;
        $processingResult = false;

        $structureManager->getElementsByIdList($paymentMethodElementId, $structureManager->rootElementId);
        if ($paymentMethodElementId && $paymentMethodElement = $structureManager->getElementById($paymentMethodElementId)
        ) {
            if ($paymentMethod = $paymentsManager->getPaymentMethod($paymentMethodElement->getName())) {
                $paymentMethod->setAttributes($paymentMethodElement->getSpecialData($this->getService('languagesManager')
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
                $structureManager->getElementsByIdList($transactionCode, $structureManager->rootElementId);
                if ($paymentElement = $structureManager->getElementById($transactionCode)) {
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
            }
        }
    }
}