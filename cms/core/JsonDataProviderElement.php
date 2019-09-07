<?php


trait JsonDataProviderElement
{
    use DataResponseConverterFactory;

    public function getElementData($preset = null)
    {
        if ($converter = $this->getConverter($this->structureType)) {
            if ($converter instanceof PresetDataResponseConverterInterface) {
                $converter->setPreset($preset);
            }
            if ($data = $converter->convert([$this])) {
                return reset($data);
            }
        }
        return false;
    }

    public function getJsonInfo($preset = null)
    {
        if ($data = $this->getElementData($preset)) {
            return json_encode($data);
        }
        return false;
    }
}