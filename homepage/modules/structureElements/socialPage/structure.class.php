<?php

class socialPageElement extends structureElement
{
    public $dataResourceName = 'module_social_page';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['socialId'] = 'text';
    }
}