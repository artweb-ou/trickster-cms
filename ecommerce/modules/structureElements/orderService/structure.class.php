<?php

class orderServiceElement extends structureElement
{
    public $dataResourceName = 'module_order_service';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['serviceId'] = 'text';
        $moduleStructure['price'] = 'text';
    }
}

