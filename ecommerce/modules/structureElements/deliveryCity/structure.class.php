<?php

/**
 * Class deliveryCityElement
 *
 * @property string $title
 */
class deliveryCityElement extends structureElement
{
    public $dataResourceName = 'module_delivery_city';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }
}