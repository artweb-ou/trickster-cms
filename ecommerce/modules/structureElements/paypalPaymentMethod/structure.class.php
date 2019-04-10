<?php

class paypalPaymentMethodElement extends paymentMethodElement
{
    public function getSpecialFields()
    {
        return [
            "sellerName" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "certificateId" => [
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
            "publicCertificateFile" => [
                "format" => "file",
                "multiLanguage" => false,
            ],
            "publicCertificateFileName" => [
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