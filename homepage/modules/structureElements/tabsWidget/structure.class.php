<?php

class tabsWidgetElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_tabswidget';
    protected $allowedTypes = [
        'article',
        'widget',
        'gallery',
        'service',
        'production',
        'brandsList',
        'personnelList',
        'feedback',
        'map',
        'productCatalogue',
        'linkList',
        'selectedProducts',
        'selectedGalleries',
        'registration',
        'passwordReminder',
        'purchaseHistory',
        'bannerCategory',
        'latestNews',
        'eventsList',
        'newsList',
        'productSearch',
    ];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hidden'] = 'checkbox';
    }

    public function getContentList()
    {
        $this->contentList = $this->getChildrenList('content');
        return $this->contentList;
    }

    public function getParent()
    {
        return $this->getService('structureManager')->getElementsFirstParent($this->id);
    }
}