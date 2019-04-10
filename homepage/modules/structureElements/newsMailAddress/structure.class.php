<?php

class newsMailAddressElement extends structureElement
{
    public $dataResourceName = 'module_newsmailaddress';
    protected $allowedTypes = [];
    public $defaultActionName = 'showForm';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['personalName'] = 'text';
        $moduleStructure['email'] = 'email';
        $moduleStructure['groups'] = 'numbersArray';
    }

    public function getTitle()
    {
        return ($this->email) ? $this->email : parent::getTitle();
    }
}


