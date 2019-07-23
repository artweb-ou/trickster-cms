<?php

class productionElement extends menuStructureElement
{
    public $dataResourceName = 'module_production';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'container';
    public $feedbackURL = false;
    public $galleriesList = [];
    public $feedbackFormsList = false;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['content'] = 'html';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image'] = 'image';
        $moduleStructure['galleries'] = 'array';
        $moduleStructure['feedbackId'] = 'text';
        $moduleStructure['originalName2'] = 'fileName';
        $moduleStructure['file'] = 'file';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['formRelativesInput'] = 'array';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
            'showlanguageForm',
        ];
    }
}