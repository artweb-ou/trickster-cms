<?php

class orderFieldElement extends structureElement
{
    public $dataResourceName = 'module_order_field';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['fieldId'] = 'text';
        $moduleStructure['fieldName'] = 'text';
        $moduleStructure['value'] = 'text';
    }
}

