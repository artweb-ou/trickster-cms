<?php

class everypayPaymentMethodElement extends paymentMethodElement
{
    public function getSpecialFields()
    {
        return array(
            "projectId"    => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
            "account"    => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
            "controlCode"    => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
            "bankURL"    => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
        );
    }
}

