<?php

class tdBalticImportPluginElement extends importPluginElement
{
    public function getSpecialFields()
    {
        return [
            "orgNumber" => [
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
                && !empty($specialData['orgNumber'])
            ) {
                $this->warehouse = new TdBalticWarehouse(
                    $specialData['orgNumber'],
                    $specialData['username'],
                    $specialData['password']
                );
            }
        }
        return $this->warehouse;
    }

    public function getOriginName()
    {
        return TdBalticWarehouse::CODE;
    }
}

