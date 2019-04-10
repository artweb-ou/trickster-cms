<?php
include_once(controller::getInstance()->getPathsManager()->getPath('paymentMethods') . 'paysera/WebToPay.php');

class payseraPaymentsMethod extends paymentsMethod
{
    protected $projectId = false;
    protected $signPassword = false;
    protected $externalCurrencyName = false;
    protected $explanationText = false;
    protected $transactionCode = false;
    protected $languageCode = false;
    protected $paymentAmount = false;
    protected $currencyName = false;
    protected $bankCode = false;
    protected $returnURL = false;
    protected $country = false;
    protected $configurationFileName = 'configuration.xml';

    public function __construct()
    {
        $this->setClassFilePath();
    }

    public function loadExternalFiles()
    {
    }

    protected function loadConfiguration()
    {
        $filePath = $this->classFilePath . '/' . $this->configurationFileName;
        if (file_exists($filePath)) {
            if ($xml = simplexml_load_file($filePath)) {
                foreach ($xml->account->children() as $parameter => $value) {
                    if (strlen((string)$value)) {
                        $this->$parameter = (string)$value;
                    }
                }
            }
        }
    }

    public function setTransactionCode($value)
    {
        $this->transactionCode = $value;
    }

    public function setPaymentAmount($value)
    {
        $this->paymentAmount = floatval($value) * 100;
    }

    public function setCurrencyName($value)
    {
        $this->externalCurrencyName = strtolower($value);
    }

    public function setReturnURL($value)
    {
        $this->returnURL = $value;
    }

    public function setReferenceNumber($value)
    {
        $this->referenceNumber = $value;
    }

    public function setExplanationText($value)
    {
        $this->explanationText = $value;
    }

    public function setLanguageCode($value)
    {
        $this->languageCode = strtoupper($value);
    }

    public function getTransactionResult()
    {
        $transactionResult = 'fail';
        try {
            if ($data = WebToPay::validateAndParseData($_GET, $this->projectId, $this->signPassword)) {
                $this->transactionCode = $data['orderid'];
                $this->paymentAmount = $data['amount'];
                if (isset($data['p_firstname']) && isset($data['p_lastname'])) {
                    $this->payerName = $data['p_firstname'] . ' ' . $data['p_lastname'];
                }
                $this->currencyName = $data['currency'];
                $this->payerAccount = $data['p_email'];
                if ($data['status'] == 1) {
                    $transactionResult = 'success';
                    $this->paymentDate = time();
                } elseif ($data['status'] == 2) {
                    $transactionResult = 'deferred';
                }
            }
        } catch (Exception $e) {
        }

        return $transactionResult;
    }

    public function getTransactionData()
    {
        try {
            ob_end_clean();
            WebToPay::redirectToPayment([
                'projectid' => $this->projectId,
                'sign_password' => $this->signPassword,
                'orderid' => $this->transactionCode,
                'amount' => $this->paymentAmount,
                'currency' => $this->externalCurrencyName,
                'country' => $this->country,
                'accepturl' => $this->returnURL,
                'cancelurl' => $this->returnURL,
                'callbackurl' => $this->returnURL,
                'paytext' => $this->explanationText,
                'lang' => $this->languageCode,
                'p_email' => $this->payerEmail,
                'test' => 0,
            ]);
        } catch (WebToPayException $e) {
            return false;
        }
        return true;
    }

    public function getTransactionCode()
    {
        return $this->transactionCode;
    }

    public function getBankCode()
    {
        return $this->bankCode;
    }

    public function getBankTransactionNumber()
    {
        return $this->bankTransactionNumber;
    }

    public function getPaymentAmount()
    {
        return $this->paymentAmount / 100;
    }

    public function getPayerName()
    {
        if ($this->payerName) {
            return $this->payerName;
        }
        return $this->payerAccount;
    }

    public function getPayerAccount()
    {
        return $this->payerAccount;
    }

    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    public function getPaymentCurrency()
    {
        return $this->currencyName;
    }

    protected function setClassFilePath()
    {
        $this->classFilePath = dirname(__FILE__);
    }

    public function getRequestType()
    {
        return 'internal';
    }
}