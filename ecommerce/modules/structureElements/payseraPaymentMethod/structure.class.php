<?php

class payseraPaymentMethodElement extends paymentMethodElement
{
    public function getSpecialFields()
    {
        return [
            "projectId" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "signPassword" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
        ];
    }
}