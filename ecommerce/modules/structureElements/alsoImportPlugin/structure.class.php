<?php

class alsoImportPluginElement extends importPluginElement
{
    public function getSpecialFields()
    {
        return [
            "clientId" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "username" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
            "password" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
        ];
    }

    public function getWarehouse()
    {
        if ($this->warehouse === null) {
            $specialData = $this->getSpecialData($this->getService('LanguagesManager')->getCurrentLanguageId());
            if ($specialData && !empty($specialData['username']) && !empty($specialData['password'])
                && !empty($specialData['clientId'])
            ) {
                $this->warehouse = new AlsoWarehouse($specialData['clientId'], $specialData['username'], $specialData['password']);
            }
        }
        return $this->warehouse;
    }

    public function getOriginName()
    {
        return AlsoWarehouse::CODE;
    }
}

