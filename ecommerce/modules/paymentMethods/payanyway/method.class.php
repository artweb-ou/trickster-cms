<?php

class payanywayPaymentsMethod extends paymentsMethod implements TransactionResponsePaymentMethodInterface, OrderDataPaymentMethodInterface
{
    protected $projectId = '';
    protected $testMode = '0';
    protected $controlCode = '';
    protected $bankURL = 'https://www.payanyway.ru/assistant.htm';

    protected $externalCurrencyName = false;

    protected $explanationText = false;
    protected $transactionCode = false;
    protected $languageCode = false;
    protected $paymentAmount = false;
    protected $currencyName = false;
    protected $bankCode = false;
    protected $returnURL = false;
    protected $country = false;
    protected $orderData = false;

    public function __construct()
    {
        $this->setClassFilePath();
    }

    public function loadExternalFiles()
    {

    }

    public function setTransactionCode($value)
    {
        $this->transactionCode = $value;
    }

    public function setPaymentAmount($value)
    {
        $this->paymentAmount = sprintf('%0.2f', floatval($value));
    }

    public function setCurrencyName($value)
    {
        $this->externalCurrencyName = strtoupper($value);
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
        $code = strtolower($value);
        if ($code == 'RUS') {
            $this->languageCode = 'ru';
        } else {
            $this->languageCode = 'en';
        }
    }

    public function getTransactionResult()
    {
        $this->setTransactionCode($_POST['MNT_TRANSACTION_ID']);
        $this->setPaymentAmount($_POST['MNT_AMOUNT']);
        $this->setCurrencyName($_POST['MNT_CURRENCY_CODE']);

        $check = md5(
            $_POST['MNT_ID'] .
            $_POST['MNT_TRANSACTION_ID'] .
            $_POST['MNT_OPERATION_ID'] .
            $_POST['MNT_AMOUNT'] .
            $_POST['MNT_CURRENCY_CODE'] .
            $_POST['MNT_SUBSCRIBER_ID'] .
            $_POST['MNT_TEST_MODE'] .
            $this->controlCode
        );

        if ($check == $_POST['MNT_SIGNATURE']) {
            $transactionResult = 'success';
        } else {
            $transactionResult = 'invalid';
        }

        return $transactionResult;
    }

    public function getTransactionData()
    {
        $data = array(
            "MNT_ID"             => $this->projectId,
            "MNT_AMOUNT"         => $this->paymentAmount,
            "MNT_TRANSACTION_ID" => $this->transactionCode,
            "MNT_CURRENCY_CODE"  => $this->externalCurrencyName,
            "MNT_TEST_MODE"      => $this->testMode,
            "MNT_DESCRIPTION"    => $this->explanationText,
            "MNT_SUBSCRIBER_ID"  => '',
            "MNT_SIGNATURE"      => '',
            "MNT_SUCCESS_URL"    => $this->returnURL,
            "MNT_INPROGRESS_URL" => $this->returnURL,
            "MNT_FAIL_URL"       => $this->returnURL,
            "MNT_RETURN_URL"     => $this->returnURL,
            "moneta.locale"      => $this->languageCode,
            "MNT_CUSTOM1"        => 1,
            "MNT_CUSTOM2"        => $this->generateCashRegisterJson(),
        );

        $signature = md5(
            $data['MNT_ID'] .
            $data['MNT_TRANSACTION_ID'] .
            $data['MNT_AMOUNT'] .
            $data['MNT_CURRENCY_CODE'] .
            $data['MNT_SUBSCRIBER_ID'] .
            $data['MNT_TEST_MODE'] .
            $this->controlCode
        );
        $data['MNT_SIGNATURE'] = $signature;

        $html = '';
        $html .= '<form action="' . $this->bankURL . '" method="post" enctype="multipart/form-data">';
        foreach ($data as $name => &$parameter) {
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
        return 'post';
    }

    public function getTransactionResponse($processingResult)
    {
        if ($processingResult) {
            return "SUCCESS";
        } else {
            return "FAIL";
        }
    }

    protected function generateCashRegisterJson()
    {
        $data = [];
        if ($this->orderData) {
            $data['customer'] = $this->orderData['payerEmail'];
            $data['items'] = [];
            foreach ($this->orderData['addedProducts'] as $productInfo) {
                $title = $productInfo['title'];
                if (strlen($title) > 20) {
                    $title = mb_substr($title, 0, 20) . '...';
                }
                $data['items'][] = [
                    'n' => $title,
                    'p' => $productInfo['price'],
                    'q' => $productInfo['amount'],
                    't' => 1102,
                ];
            }
        }
        $json = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
            return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
        }, json_encode($data));

        return $json;
    }

    public function setOrderData($orderData)
    {
        $this->orderData = $orderData;
    }

}

