<?php

class widgetElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_widget';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['code'] = 'code';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
    }
}


