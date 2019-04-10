<?php
include_once(controller::getInstance()->getPathsManager()->getPath('paymentMethods') . 'paypal/EWPServices.php');

class paypalPaymentsMethod extends paymentsMethod
{
    protected $bankURL = false;
    protected $certificateId = false;
    protected $privateKeyFile = false;
    protected $bankCertificateFile = false;
    protected $publicCertificateFile = false;
    protected $buttonImage = false;
    protected $encoding = false;
    protected $cmd = false;
    protected $externalCurrencyName = false;
    protected $sellerAccount = false;
    protected $sellerName = false;
    protected $sellerCode = false;
    protected $explanationText = false;
    protected $transactionCode = false;
    protected $languageCode = false;
    protected $paymentAmount = false;
    protected $currencyName = false;
    protected $bankCode = false;
    protected $privateKeyPath = false;
    protected $bankCertificatePath = false;
    protected $publicCertificatePath = false;
    protected $configurationFileName = 'configuration.xml';

    public function __construct()
    {
        $this->setClassFilePath();
        $this->loadConfiguration();
    }

    public function loadExternalFiles()
    {
        $this->privateKeyPath = $this->certificatesPath . $this->privateKeyFile;
        $this->bankCertificatePath = $this->certificatesPath . $this->bankCertificateFile;
        $this->publicCertificatePath = $this->certificatesPath . $this->publicCertificateFile;
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
        $transactionResult = 'fail';

        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            if (!is_array($value)) {
                $value = urlencode($value);
                $req .= "&" . $key . "=" . $value;
            }
        }

        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        if ($fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30)) {
            fputs($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets($fp, 1024);
                if (strcmp($res, "VERIFIED") == 0) {
                    $transactionResult = 'success';

                    $this->transactionCode = $_POST['custom'];
                    $this->payerName = $_POST['payer_email'];
                    $this->payerAccount = $_POST['payer_id'];
                    $this->paymentAmount = $_POST['mc_gross'];
                    $this->paymentDate = $_POST['payment_date'];
                    $this->currencyName = $_POST['mc_currency'];
                } elseif (strcmp($res, "INVALID") == 0) {
                }
            }
            fclose($fp);
        }

        return $transactionResult;
    }

    public function getTransactionData()
    {
        if ($this->externalCurrencyName != 'eur') {
            $this->paymentAmount = round($this->paymentAmount / $this->euroCourse, 2);
        }

        $buttonParams = [
            "cmd" => $this->cmd,
            "business" => $this->sellerName,
            "cert_id" => $this->certificateId,
            "charset" => $this->encoding,
            "item_name" => htmlspecialchars($this->explanationText),
            "item_number" => htmlspecialchars($this->transactionCode),
            "amount" => htmlspecialchars($this->paymentAmount),
            "currency_code" => htmlspecialchars($this->currencyName),
            "return" => htmlspecialchars($this->returnURL),
            "cancel_return" => htmlspecialchars($this->returnURL),
            "notify_url" => htmlspecialchars($this->returnURL),
            "custom" => $this->transactionCode,
        ];

        $buttonReturn = EWPServices::encryptButton($buttonParams, realpath($this->publicCertificatePath), realpath($this->privateKeyPath), false, realpath($this->bankCertificatePath), $this->bankURL, $this->buttonImage);
        if ($buttonReturn['status'] === true) {
            return $buttonReturn['encryptedButton'];
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