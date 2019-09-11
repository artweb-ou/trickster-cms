<?php

class instagramImageElement extends structureElement
{
    public $dataResourceName = 'module_instagram_image';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['instagramId'] = 'text';
        $moduleStructure['image'] = 'text';
        $moduleStructure['pageSocialId'] = 'text';
        $moduleStructure['link'] = 'text';
    }
}
