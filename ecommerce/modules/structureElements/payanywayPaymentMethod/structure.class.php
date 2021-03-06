<?php

class payanywayPaymentMethodElement extends paymentMethodElement
{
    public function getSpecialFields()
    {
        return array(
            "projectId"    => array(
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
            "testMode" => array(
                "format"        => "text",
                "multiLanguage" => false,
            ),
        );
    }

    protected function setTabs() {
        $this->setTabsTemplate('paymentMethod.tabs.tpl');
    }
}

