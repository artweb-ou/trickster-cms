<?php

class everypayPaymentsMethod extends paymentsMethod
{
    protected $projectId = '';
    protected $account = '';
    protected $testMode = '1';
    protected $controlCode = '';
    protected $bankURL;

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
        $transactionResult = 'invalid';

        $this->bankTransactionNumber = $_GET['payment_reference'];
        $parameters = [
            'api_username' => $this->projectId,
        ];
        $query = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
        if ($response = $this->callAPI(
            "GET",
            $this->bankURL . "/payments/" . $this->bankTransactionNumber . '?' . $query
        )) {
            if ($data = json_decode($response, true)) {
                if (!empty($data['cc_details'])) {
                    $this->payerName = $data['cc_details']['holder_name'];
                    $this->payerAccount = $data['cc_details']['issuer_country'] . '; ' . $data['cc_details']['issuer'] . '; ' . $data['cc_details']['type'];
                }
                $this->paymentDate = $data['payment_created_at'];
                $this->paymentAmount = $data['standing_amount'];
                $this->transactionCode = $data['order_reference'];
                if ($data['payment_state'] === 'settled') {
                    $transactionResult = 'success';
                } else {
                    $transactionResult = 'failed';
                }
            }
        }

        return $transactionResult;
    }

    private function callAPI($method, $url, $data = false)
    {
        $curl = curl_init();
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }
        $username = $this->projectId; // API USERNAME FROM GENERAL SETTINGS
        $password = $this->controlCode; // API SECRET FROM GENERAL SETTINGS
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password"); //HTTP BASIC AUTH

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return $result;
    }

    public function getTransactionData()
    {
        $parameters = [
            "timestamp" => date('c'),
            "api_username" => $this->projectId, // API USERNAME FROM GENERAL SETTINGS
            "account_name" => $this->account, //NAME OF PROCESSING ACCOUNT FROM THE PORTAL
            "amount" => $this->paymentAmount,
            "order_reference" => $this->transactionCode,
            "nonce" => uniqid(),
            "customer_url" => $this->returnURL,
        ];

        $query = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);

        if ($response = $this->callAPI(
            "POST",
            $this->bankURL . "/payments/oneoff",
            $query
        )) {
            if ($data = json_decode($response, true)) {
                if (!empty($data['payment_link'])) {
                    return $data['payment_link'];
                }
            }
        }
        return false;
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
        return 'get';
    }

}

