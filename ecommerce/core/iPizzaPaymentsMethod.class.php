<?php

abstract class iPizzaPaymentsMethod extends paymentsMethod
{
    protected $defaultServiceNumber = '1012';
    protected $servicesData = false;
    protected $privateKeyText = false;
    protected $bankCertificateText = false;
    protected $bankURL = false;
    protected $configurationFileName = 'configuration.xml';
    protected $privateKeyFile = false;
    protected $bankCertificateFile = false;
    protected $protocolVersion = false;
    protected $serviceNumber = false;
    protected $classFilePath = false;
    protected $returnURL = false;
    protected $externalMAC = false;
    protected $requestType = 'get';

    public function __construct()
    {
        $this->setClassFilePath();
        $this->loadConfiguration();
    }

    public function loadExternalFiles()
    {
        $this->loadPrivateKeyText();
        $this->loadCertificateText();
    }

    public function getTransactionData()
    {
        $this->setServiceNumber($this->defaultServiceNumber);
        $this->setTransactionDate(time());
        $transactionData = false;
        if ($MACParameters = $this->prepareMACParameters()) {
            if ($MACString = $this->generateMACString($MACParameters)) {
                if ($this->generateMAC($MACString)) {
                    if ($this->requestType == 'post') {
                        $transactionData = $this->generatePostForm();
                    } else {
                        $transactionData = $this->generateGetURL();
                    }
                }
            }
        }
        return $transactionData;
    }

    public function getTransactionResult()
    {
        $transactionResult = 'invalid';
        if ($serviceNumber = $this->getExternalServiceNumber()) {
            $this->setServiceNumber($serviceNumber);
            $this->receiveExternalParameters();
            if ($MACParameters = $this->prepareMACParameters()) {
                if ($MACString = $this->generateMACString($MACParameters)) {
                    $this->externalMAC = base64_decode($this->externalMAC);
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
        if ($this->serviceNumber == '1911') {
            $transactionResult = 'fail';
        } elseif ($this->serviceNumber == '1111') {
            $transactionResult = 'success';
        } else {
            $transactionResult = 'invalid';
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

    protected function getExternalServiceNumber()
    {
        $serviceNumber = false;
        if (isset($_REQUEST['VK_SERVICE'])) {
            $serviceNumber = $_REQUEST['VK_SERVICE'];
        }
        return $serviceNumber;
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
                            $MACParameters[] = $parameterValue;
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

    protected function generateMAC($MACString)
    {
        $result = false;
        if ($this->privateKeyText) {
            $privateKeyResource = openssl_pkey_get_private($this->privateKeyText);

            $signature = '';
            openssl_sign($MACString, $signature, $privateKeyResource);

            if ($this->MAC = base64_encode($signature)) {
                $result = true;
            }
            openssl_free_key($privateKeyResource);
        }
        return $result;
    }

    protected function verifyMAC($MACString, $externalMAC)
    {
        $result = false;
        if ($this->bankCertificateText) {
            $publicKeyResource = openssl_pkey_get_public($this->bankCertificateText);
            if (openssl_verify($MACString, $externalMAC, $publicKeyResource)) {
                $result = true;
            }
            openssl_free_key($publicKeyResource);
        }
        return $result;
    }

    protected function generatePostForm()
    {
        $html = '';
        $html .= '<form action="' . $this->bankURL . '" method="post">';
        if (isset($this->servicesData[$this->serviceNumber])) {
            foreach ($this->servicesData[$this->serviceNumber] as $parameter) {
                $parameterName = $parameter['name'];
                $parameterRelation = $parameter['relation'];
                $parameterValue = $this->$parameterRelation;
                $html .= '<input name="' . $parameterName . '" value="' . htmlspecialchars($parameterValue, ENT_QUOTES) . '" />';
            }
        }
        $html .= '</form>';
        return $html;
    }

    protected function generateGetURL()
    {
        $getURL = false;
        if (isset($this->servicesData[$this->serviceNumber])) {
            foreach ($this->servicesData[$this->serviceNumber] as $parameter) {
                $parameterName = $parameter['name'];
                $parameterRelation = $parameter['relation'];
                $parameterValue = $this->$parameterRelation;
                if (!$getURL) {
                    $getURL = $this->bankURL . '?';
                } else {
                    $getURL .= '&';
                }
                $getURL .= $parameterName . '=' . urlencode($parameterValue);
            }
        }

        return $getURL;
    }

    protected function loadPrivateKeyText()
    {
        $filePath = $this->certificatesPath . $this->privateKeyFile;
        if (file_exists($filePath)) {
            $this->privateKeyText = file_get_contents($filePath);
        } else {
            $this->logError('Missing private key file "' . $this->privateKeyFile . '"');
        }
    }

    protected function loadCertificateText()
    {
        $filePath = $this->certificatesPath . $this->bankCertificateFile;
        if (file_exists($filePath)) {
            $this->bankCertificateText = file_get_contents($filePath);
        } else {
            $this->logError('Missing public certificate file "' . $this->bankCertificateFile . '"');
        }
    }

    public function getRequestType()
    {
        return $this->requestType;
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
        $this->transactionDate = date(DateTime::ISO8601, $timeStamp); //ISO 8601
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
        $this->paymentAmount = sprintf('%01.2f', $value);
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
        $this->languageCode = mb_strtoupper($value);
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

    protected function generateMACString($MACParameters)
    {
        $MACString = '';
        foreach ($MACParameters as &$parameter) {
            $MACString .= str_pad(mb_strlen($parameter), 3, '0', STR_PAD_LEFT) . $parameter;
        }
        return $MACString;
    }
}
