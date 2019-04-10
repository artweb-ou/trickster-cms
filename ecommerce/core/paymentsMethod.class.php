<?php

abstract class paymentsMethod extends errorLogger
{
    protected $bankURL = false;
    protected $charset = 'UTF-8';
    protected $sellerAccount = false;
    protected $sellerName = false;
    protected $sellerCode = false;
    protected $explanationText = false;
    protected $transactionCode = false;
    protected $referenceNumber = false;
    protected $languageCode = false;
    protected $paymentAmount = false;
    protected $currencyName = false;
    protected $certificatesPath = "";
    protected $euroCourse = false;
    protected $bankTransactionNumber = false;
    protected $bankCode = false;
    protected $payerAccount = false;
    protected $payerName = false;
    protected $payerEmail;
    protected $paymentDate = false;
    protected $transactionDate = false;

    abstract public function setTransactionCode($value);

    abstract public function setPaymentAmount($value);

    abstract public function setCurrencyName($value);

    abstract public function setReturnURL($value);

    abstract public function setReferenceNumber($value);

    abstract public function setExplanationText($value);

    abstract public function setLanguageCode($value);

    abstract public function getTransactionData();

    abstract public function getTransactionResult();

    abstract public function getTransactionCode();

    abstract public function getBankCode();

    abstract public function getBankTransactionNumber();

    abstract public function getPaymentAmount();

    abstract public function getPaymentCurrency();

    abstract public function getPayerName();

    abstract public function getPayerAccount();

    abstract public function getPaymentDate();

    abstract public function getRequestType();

    public function setEuroCourse($euroCourse)
    {
        $this->euroCourse = $euroCourse;
    }

    public function setAttributes($attributes)
    {
        foreach ($attributes as $key => &$value) {
            $this->$key = $value;
        }
    }

    public function setCertificatesPath($path)
    {
        $this->certificatesPath = $path;
    }

    public function loadExternalFiles()
    {
    }

    public function setPayerEmail($email)
    {
        $this->payerEmail = $email;
    }
}