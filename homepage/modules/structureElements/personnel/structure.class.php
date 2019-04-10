<?php

class personnelElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_personnel';
    public $defaultActionName = 'show';
    public $role = 'content';
    public $menusList = [];

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['status'] = 'text';
        $moduleStructure['position'] = 'text';
        $moduleStructure['phone'] = 'text';
        $moduleStructure['mobilePhone'] = 'text';
        $moduleStructure['email'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['content'] = 'html';
    }
}


