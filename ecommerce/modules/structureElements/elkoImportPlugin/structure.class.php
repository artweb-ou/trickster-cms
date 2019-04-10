<?php

class elkoImportPluginElement extends importPluginElement
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
            $username = '';
            $password = '';
            $specialData = $this->getSpecialData($this->getService('languagesManager')->getCurrentLanguageId());
            if ($specialData) {
                $username = $specialData['username'];
                $password = $specialData['password'];
            }
            $this->warehouse = new ElkoWarehouse($username, $password);
        }
        return $this->warehouse;
    }

    public function getOriginName()
    {
        return ElkoWarehouse::CODE;
    }
}

