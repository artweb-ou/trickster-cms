<?php

class estcardPaymentsMethod extends iPizzaPaymentsMethod
{
    protected $defaultServiceNumber = 'gaf';
    protected $delivery = 'T';
    protected $charset = 'UTF-8';

    public function setServiceNumber($value)
    {
        $this->serviceNumber = $value;
    }

    public function setTransactionCode($value)
    {
        $this->transactionCode = $value + 1000000;
    }

    public function setPaymentAmount($value)
    {
        $this->paymentAmount = $value * 100;
    }

    public function setReferenceNumber($value)
    {
        $this->referenceNumber = $value;
    }

    public function setExplanationText($value)
    {
        $this->explanationText = $value;
    }

    public function setReturnURL($value)
    {
        $this->returnURL = $value;
    }

    public function setLanguageCode($value)
    {
        if (mb_strtolower($value) == 'est') {
            $this->languageCode = 'et';
        } else {
            $this->languageCode = 'en';
        }
    }

    public function getTransactionCode()
    {
        return $this->transactionCode - 1000000;
    }

    public function getBankCode()
    {
        return 'estcard';
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
        return $this->payerName;
    }

    public function getPayerAccount()
    {
        return $this->payerAccount;
    }

    public function getPaymentDate()
    {
        $date = date('d.m.Y H:i:s', strtotime($this->paymentDate));
        return $date;
    }

    public function getTransactionResult()
    {
        $transactionResult = 'invalid';
        if ($serviceNumber = $this->getExternalServiceNumber()) {
            $this->setServiceNumber($serviceNumber);
            $this->receiveExternalParameters();
            if ($MACParameters = $this->prepareMACParameters()) {
                if ($MACString = $this->generateMACString($MACParameters)) {
                    $this->externalMAC = pack("H*", $this->externalMAC);
                    if ($this->verifyMAC($MACString, $this->externalMAC)) {
                        $transactionResult = $this->getTransactionResultText();
                    }
                }
            }
        }

        return $transactionResult;
    }

    protected function getTransactionResultText()
    {
        if ($this->responseCode === '000') {
            $transactionResult = 'success';
        } else {
            $transactionResult = 'fail';
        }
        return $transactionResult;
    }

    protected function getExternalServiceNumber()
    {
        $serviceNumber = false;
        if (isset($_REQUEST['action'])) {
            $serviceNumber = $_REQUEST['action'];
        }
        return $serviceNumber;
    }

    protected function setClassFilePath()
    {
        $this->classFilePath = dirname(__FILE__);
    }

    protected function prepareMACParameters()
    {
        $result = false;

        $MACParameters = [];
        if (isset($this->servicesData[$this->serviceNumber])) {
            $validated = true;
            foreach ($this->servicesData[$this->serviceNumber] as $parameter) {
                $parameterRelation = $parameter['relation'];
                $parameterLength = $parameter['length'];
                $parameterMac = $parameter['MAC'];

                if ($parameterMac == '1') {
                    if (isset($this->$parameterRelation) && ($this->$parameterRelation !== false)) {
                        $parameterValue = $this->$parameterRelation;
                        if (strlen($parameterValue) <= $parameterLength) {
                            $parameter['value'] = $parameterValue;
                            $MACParameters[] = $parameter;
                        } else {
                            $validated = false;
                            $this->logError('Wrong length for "' . $parameterRelation . '" (' . strlen($parameterValue) . '/' . $parameterLength . ')');
                        }
                    } else {
                        $validated = false;
                        $this->logError('Missing value for "' . $parameterRelation . '"');
                    }
                }
            }
        } else {
            $validated = false;
            $this->logError('Missing XML parameters for "' . $this->serviceNumber . '"');
        }

        if ($validated && count($MACParameters) > 0) {
            $result = $MACParameters;
        }
        return $result;
    }

    protected function generateMACString($MACParameters)
    {
        $MACString = '';
        foreach ($MACParameters as &$parameter) {
            if (is_numeric($parameter['value'])) {
                $MACString .= $this->mb_str_pad($parameter['value'], $parameter['length'], '0', STR_PAD_LEFT);
            } else {
                $MACString .= $this->mb_str_pad($parameter['value'], $parameter['length'], ' ', STR_PAD_RIGHT);
            }
        }
        return $MACString;
    }

    protected function mb_str_pad($input, $pad_length, $pad_string, $pad_style, $encoding = "UTF-8")
    {
        return str_pad($input,
            strlen($input) - mb_strlen($input, $encoding) + $pad_length, $pad_string, $pad_style);
    }

    protected function generateMAC($MACString)
    {
        $result = false;
        if ($this->privateKeyText) {
            $privateKeyResource = openssl_pkey_get_private($this->privateKeyText);

            $signature = '';
            openssl_sign($MACString, $signature, $privateKeyResource, OPENSSL_ALGO_SHA1);

            if ($this->MAC = bin2hex($signature)) {
                $result = true;
            }
            openssl_free_key($privateKeyResource);
        }
        return $result;
    }

    public function setTransactionDate($timeStamp)
    {
        $this->transactionDate = date('YmdHis', $timeStamp); //AAAAKKPPTTmmss
    }
}
