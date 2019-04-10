<?php

class deliveryCountryElement extends structureElement
{
    public $dataResourceName = 'module_delivery_country';
    protected $allowedTypes = ['deliveryCity'];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['iso3166_1a2'] = 'text';
        $moduleStructure['conditionsText'] = 'html';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'conditionsText';
    }
}

