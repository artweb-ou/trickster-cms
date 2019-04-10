<?php

class moneybookersPaymentMethodElement extends paymentMethodElement
{
    public function getSpecialFields()
    {
        return [
            "sellerName" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "sellerAccount" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "sellerWord" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "sellerWordMD5" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
        ];
    }
}