<?php

class nordeaPaymentMethodElement extends paymentMethodElement
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
            "macKey" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
        ];
    }
}