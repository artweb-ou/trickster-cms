<?php

class accImportPluginElement extends importPluginElement
{
    public function getSpecialFields()
    {
        return [
            "LicenseKey" => [
                "format" => "text",
                "multiLanguage" => false,
            ],
        ];
    }

    public function getWarehouse()
    {
        if ($this->warehouse === null) {
            $licenseKey = '';
            $specialData = $this->getSpecialData($this->getService('LanguagesManager')->getCurrentLanguageId());
            if ($specialData && !empty($specialData['LicenseKey'])) {
                $licenseKey = $specialData['LicenseKey'];
            }
            $this->warehouse = new AcmeWarehouse($licenseKey);
        }
        return $this->warehouse;
    }

    public function getOriginName()
    {
        return AcmeWarehouse::CODE;
    }
}

