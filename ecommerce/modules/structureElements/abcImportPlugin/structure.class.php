<?php

class abcImportPluginElement extends importPluginElement
{
    public function getSpecialFields()
    {
        return [
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
            $specialData = $this->getSpecialData($this->getService('languagesManager')->getCurrentLanguageId());
            if ($specialData && !empty($specialData['username']) && !empty($specialData['password'])) {
                $this->warehouse = new AbcWarehouse($specialData['username'], $specialData['password']);
            }
        }
        return $this->warehouse;
    }

    public function getOriginName()
    {
        return AbcWarehouse::CODE;
    }
}

