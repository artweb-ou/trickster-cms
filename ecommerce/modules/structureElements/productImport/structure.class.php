<?php

class productImportElement extends structureElement
{
    protected $allowedTypes = ['productImportTemplate'];
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    public $defaultActionName = 'showForm';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['categoryId'] = 'text';
        $moduleStructure['languageCode'] = 'text';
        $moduleStructure['importFile'] = 'file';
        $moduleStructure['templateId'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    protected function getTabsList()
    {
        return [
            'showImportForm',
            'showFullList',
            'showForm',
            'showPrivileges',
        ];
    }
}