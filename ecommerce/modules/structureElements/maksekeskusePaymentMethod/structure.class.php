<?php

class maksekeskusePaymentMethodElement extends paymentMethodElement
{
    public function getSpecialFields()
    {
        return array(
            "shopID"    => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
            "keyPublic"    => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
            "keyPrivate"    => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
        );
    }
}

