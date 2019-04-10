<?php

class productGalleryImageElement extends structureElement implements ImageUrlProviderInterface
{
    use ImageUrlProviderTrait;

    public $dataResourceName = 'module_productgallery_image';
    protected $allowedTypes = ['productGalleryProduct'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $formFieldsList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['description'] = 'html';
        $moduleStructure['labelText'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'text';
        $moduleStructure['link'] = 'url';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showProducts',
            'showPositions',
            'showPrivileges',
        ];
    }
}
