<?php

class shoppingBasketStatusElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_shoppingbasketstatus';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['popup'] = 'checkbox';
        $moduleStructure['floating'] = 'checkbox';
    }
}


