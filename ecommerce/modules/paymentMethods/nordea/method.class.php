<?php

class nordeaPaymentsMethod extends paymentsMethod
{
    protected $classFilePath;
    protected $transactionDate;
    protected $returnURL;
    protected $keyVersion = '0001';
    protected $version;
    protected $confirm;
    protected $macKey;
    protected $servicesData;
    protected $serviceNumber;
    protected $externalMAC;
    protected $returnedPaid;
    protected $taxCode;
    protected $configurationFileName = 'configuration.xml';

    public function __construct()
    {
        $this->setClassFilePath();
        $this->loadConfiguration();
    }

    public function loadConfiguration()
    {
        $filePath = $this->classFilePath . '/' . $this->configurationFileName;
        if (file_exists($filePath)) {
            if ($xml = simplexml_load_file($filePath)) {
                $services = [];
                foreach ($xml->services->children() as $service) {
                    $serviceId = (string)$service->id;
                    $parameters = [];
                    foreach ($service->parameters->children() as $var) {
                        $parameter = [];
                        $parameter['length'] = (int)$var->length;
                        $parameter['name'] = (string)$var->name;
                        $parameter['MAC'] = (string)$var->MAC;
                        $parameter['id'] = (string)$var->id;
                        $parameter['relation'] = (string)$var->relation;

                        $parameters[$parameter['id']] = $parameter;
                    }
                    $services[$serviceId] = $parameters;
                }
                $this->servicesData = $services;

                foreach ($xml->account->children() as $parameter => $value) {
                    if (strlen((string)$value)) {
                        $this->$parameter = (string)$value;
                    }
                }
            }
        }
    }

    public function getTransactionData()
    {
        $this->setServiceNumber('send');
        $this->setReferenceNumber($this->calculateReference($this->getTransactionCode()));
        $this->setTransactionDate(time());

        $transactionData = false;
        if ($MACParameters = $this->prepareMACParameters()) {
            if ($this->MAC = $this->generateMACString($MACParameters)) {
                $transactionData = $this->generateFormHtml();
            }
        }
        return $transactionData;
    }

    public function generateFormHtml()
    {
        $html = '';
        $html .= '<form action="' . $this->bankURL . '" method="post">';
        foreach ($this->servicesData[$this->serviceNumber] as $parameter) {
            $name = $parameter['name'];
            $relation = $parameter['relation'];
            $value = htmlspecialchars($this->$relation, ENT_QUOTES);
            $html .= '<input name="' . $name . '" value="' . $value . '" />';
        }
        $html .= '</form>';
        return $html;
    }

    public function setCurrencyName($value)
    {
        $this->currencyName = strtoupper($value);
    }

    public function getPaymentCurrency()
    {
        return $this->currencyName;
    }

    public function setTransactionDate($timeStamp)
    {
        $this->transactionDate = 'EXPRESS';
    }

    public function getRequestType()
    {
        return 'post';
    }

    public function setServiceNumber($value)
    {
        $this->serviceNumber = $value;
    }

    public function setTransactionCode($value)
    {
        $this->transactionCode = $value;
    }

    public function setPaymentAmount($value)
    {
        $this->paymentAmount = $value;
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
            $this->languageCode = '4';
        } else {
            if (mb_strtolower($value) == 'lit') {
                $this->languageCode = '7';
            } else {
                if (mb_strtolower($value) == 'lat') {
                    $this->languageCode = '6';
                } else {
                    $this->languageCode = '3';
                }
            }
        }
    }

    public function getTransactionCode()
    {
        return $this->transactionCode;
    }

    public function getBankCode()
    {
        return 'nordea';
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

    public function getTransactionResult()
    {
        $transactionResult = 'invalid';
        if ($serviceNumber = $this->getExternalServiceNumber()) {
            $this->setServiceNumber($serviceNumber);
            $this->receiveExternalParameters();
            if ($serviceNumber == 'receive' && !$this->returnedPaid) {
                return 'fail';
            } else {
                if ($MACParameters = $this->prepareMACParameters()) {
                    if ($MACString = $this->generateMACString($MACParameters)) {
                        if ($MACString == $this->externalMAC) {
                            $transactionResult = $this->getTransactionResultText();
                        }
                    }
                }
            }
        }

        return $transactionResult;
    }

    protected function receiveExternalParameters()
    {
        $result = true;
        if (isset($this->servicesData[$this->serviceNumber])) {
            foreach ($this->servicesData[$this->serviceNumber] as $parameter) {
                $parameterRelation = $parameter['relation'];
                $parameterName = $parameter['name'];

                if (isset($_REQUEST[$parameterName])) {
                    $this->$parameterRelation = $_REQUEST[$parameterName];
                } else {
                    $this->logError('Missing REQUEST parameter for "' . $parameterName . '"');
                    $result = false;
                }
            }
        } else {
            $result = false;
            $this->logError('Missing XML parameters for "' . $this->serviceNumber . '"');
        }

        return $result;
    }

    protected function getTransactionResultText()
    {
        if ($this->returnedPaid) {
            $transactionResult = 'success';
        } else {
            $transactionResult = 'fail';
        }
        return $transactionResult;
    }

    protected function getExternalServiceNumber()
    {
        $serviceNumber = false;
        if (isset($_REQUEST['SOLOPMT_RETURN_VERSION'])) {
            $serviceNumber = 'receive';
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
            $relation = $parameter['relation'];
            //			if (!empty($this->$relation))
            //			{
            $MACString .= $this->$relation . '&';
            //			}
        }
        $MACString .= $this->macKey . '&';
        $MACString = strtoupper(md5($MACString));
        return $MACString;
    }

    protected function calculateReference($number)
    {
        $number = "$number";
        $len = strlen($number);
        $sum = 0;

        // reversed string
        $rnum = strrev($number);
        for ($i = 0; $i < $len; $i++) {
            switch (($i + 1) % 3) {
                case 0:
                    $sum += $rnum[$i];
                    break;
                case 1:
                    $sum += $rnum[$i] * 7;
                    break;
                case 2;
                    $sum += $rnum[$i] * 3;
                    break;
            }
        }
        $last = (10 - ($sum % 10)) % 10;
        return "$number$last";
    }
}