<?php

class cataloguesElement extends structureElement
{
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [
        "import",
        "categories",
        "catalogue",
        "productParameters",
        "brands",
        'collections',
        "currencies",
        "comments",
        "shoppingBasketServices",
        'paymentMethods',
        'productIcons',
    ];
    public $defaultActionName = 'showFullList';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }
}
