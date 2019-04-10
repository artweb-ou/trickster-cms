<?php

class moneybookersPaymentsMethod extends paymentsMethod
{
    protected $bankURL = false;
    protected $configurationFileName = 'configuration.xml';
    protected $externalCurrencyName = false;
    protected $sellerAccount = false;
    protected $sellerName = false;
    protected $sellerCode = false;
    protected $secretWordMD5 = false;
    protected $explanationText = false;
    protected $transactionCode = false;
    protected $languageCode = false;
    protected $paymentAmount = false;
    protected $currencyName = false;
    protected $bankCode = false;
    protected $logoURL = false;

    public function __construct()
    {
        $this->setClassFilePath();
        $this->loadConfiguration();
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
        $this->paymentAmount = sprintf('%01.2f', $value);
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
    }

    public function getTransactionResult()
    {
        $transactionResult = 'invalid';

        $this->transactionCode = $_POST['transaction_id'];
        $this->payerName = $_POST['pay_from_email'];
        $this->payerAccount = $_POST['pay_from_email'];
        $this->paymentAmount = $_POST['mb_amount'];
        if (isset($_POST['payment_date'])) {
            $this->paymentDate = $_POST['payment_date'];
        } else {
            $this->paymentDate = date('d.m.Y h:i');
        }
        $this->currencyName = $_POST['mb_currency'];

        $receivedMD5 = $_POST['md5sig'];

        $checkString = $_POST['merchant_id'] . $_POST['transaction_id'] . strtoupper($this->secretWordMD5) . $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status'];

        $checkMD5 = md5($checkString);
        if (strtoupper($checkMD5) == strtoupper($receivedMD5)) {
            if ($_POST['status'] == '2') {
                $transactionResult = 'success';
            } elseif ($_POST['status'] == '-1') {
                $transactionResult = 'undefined';
            } else {
                $transactionResult = 'fail';
            }
        }
        return $transactionResult;
    }

    public function getTransactionData()
    {
        if ($this->externalCurrencyName != 'eur') {
            $this->paymentAmount = round($this->paymentAmount / $this->euroCourse, 2);
        }

        $parameters = [
            "recipient_description" => $this->sellerName,
            "pay_to_email" => $this->sellerAccount,
            "transaction_id " => $this->transactionCode,
            "return_url" => $this->returnURL,
            "cancel_url" => $this->returnURL,
            "status_url" => $this->returnURL,
            "language" => 'EN',
            "logo_url" => $this->logoURL,
            "amount" => $this->paymentAmount,
            "currency" => $this->currencyName,
            "detail1_description" => 'Order.',
            "detail1_text" => $this->explanationText,
        ];

        $html = '';
        $html .= '<form action="' . $this->bankURL . '" method="post" enctype="multipart/form-data">';
        foreach ($parameters as $name => &$parameter) {
            $html .= '<input name="' . $name . '" value="' . htmlspecialchars($parameter, ENT_QUOTES) . '" />';
        }
        $html .= '</form>';
        return $html;
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
        return $this->paymentAmount;
    }

    public function getPayerName()
    {
        return $this->payerName;
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
        return 'post';
    }
}