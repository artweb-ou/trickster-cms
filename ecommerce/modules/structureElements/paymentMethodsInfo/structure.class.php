<?php

class paymentMethodsInfoElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_paymentmethodsinfo';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['link'] = 'url';
    }
}