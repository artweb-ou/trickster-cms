<?php

class danskebankPaymentMethodElement extends paymentMethodElement
{
    public function getSpecialFields()
    {
        return [
            "sellerName" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "sellerCode" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "sellerAccount" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "privateKeyFile" => [
                "format" => "file",
                "multiLanguage" => false,
            ],
            "privateKeyFileName" => [
                "format" => "text",
                "multiLanguage" => false,
                "hidden" => true,
            ],
            "bankCertificateFile" => [
                "format" => "file",
                "multiLanguage" => false,
            ],
            "bankCertificateFileName" => [
                "format" => "text",
                "multiLanguage" => false,
                "hidden" => true,
            ],
        ];
    }
}