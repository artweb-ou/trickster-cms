<?php

use Maksekeskus\Maksekeskus;

class maksekeskusePaymentsMethod extends paymentsMethod implements OrderDataPaymentMethodInterface
{
    private $mk;

    private $shopID = '';
    private $keyPublic = '';
    private $keyPrivate = '';
    private $requestType = 'post';
    private $returnURL;
    private $defaultCurrency = 'eur';
    private $orderData;

    public function __construct()
    {
        $this->setMk(new Maksekeskus($this->shopID, $this->keyPublic, $this->keyPrivate, false));
    }

    /**
     * @param Maksekeskus $mk
     */
    public function setMk(Maksekeskus $mk)
    {
        $this->mk = $mk;
    }

    /**
     * @return Maksekeskus
     */
    public function getMk(): Maksekeskus
    {
        return $this->mk;
    }

    public function setTransactionCode($value)
    {
        $this->transactionCode = $value;
    }

    public function setPaymentAmount($value)
    {
        $this->logError($value);
        $this->paymentAmount = str_replace(' ', '', sprintf('%0.2f', floatval($value)));
        $this->logError($this->paymentAmount);
    }

    public function setCurrencyName($value)
    {
        $this->currencyName = strtoupper($value);
    }

    public function setReturnURL($value)
    {
        $this->returnURL = $value;
    }

    /**
     * @return mixed
     */
    public function getReturnURL()
    {
        return $this->returnURL;
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
        } elseif ($code == 'EST') {
            $this->languageCode = 'et';
        } else {
            $this->languageCode = 'en';
        }
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return 'en';
    }

    public function getTransactionResult()
    {
        if ($returnData = json_decode($_POST['json'])) {
            $this->setTransactionCode($returnData->reference);
            $this->setReferenceNumber($returnData->transaction);
            $this->setPaymentAmount($returnData->amount);
            $this->setCurrencyName($returnData->currency);
            if (isset($returnData->original_amount)) {
                $this->setPaymentAmount($returnData->original_amount);
            }
            if (isset($returnData->original_currency)) {
                $this->setCurrencyName($returnData->original_currency);
            }
            if ($returnData->status == 'COMPLETED') {
                return 'success';
            } else {
                return 'undefined';
            }
        }
    }

    public function getRequestData()
    {
        return [
            'transaction' => [
                'amount' => $this->getPaymentAmount(),
                'currency' => $this->getPaymentCurrency(),
                'reference' => $this->getTransactionCode(),
                'merchant_data' => '',
                'transaction_url' => [
                    'return_url' => [
                        'method' => 'POST',
                        'url' => $this->getReturnURL(),
                    ],
                    'cancel_url' => [
                        'method' => 'POST',
                        'url' => $this->getReturnURL(),
                    ],
                    'notification_url ' => [
                        'method' => 'POST',
                        'url' => $this->getReturnURL(),
                    ],
                ],
            ],
            'customer' => [
                'email' => $this->orderData['payerEmail'],
                'country' => 'ee',
                'locale' => $this->getLanguageCode(),
                'ip' => $this->getVisitorIp(),
            ],
        ];
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
        if (empty($this->currencyName)) {
            return "EUR";
        }
        return $this->currencyName;
    }

    protected function setClassFilePath()
    {
        $this->classFilePath = dirname(__FILE__);
    }

    public function getRequestType()
    {
        return $this->requestType;
    }

    public function makeRequest()
    {
        $query = $this->getRequestData();
        $mk = $this->getMk();
        $transaction = $mk->createTransaction($query);
        return $transaction;
    }

    public function getTransactionData()
    {
        $result = $this->makeRequest();
        if (!empty($result)) {
            return $result->payment_methods->other[0]->url;
        }
        return false;
    }

    public function setOrderData($orderData)
    {
        $this->orderData = $orderData;
    }
}