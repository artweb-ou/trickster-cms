<?php

class selectedGalleriesElement extends structureElement
{
    public $dataResourceName = 'module_selected_galleries';
    public $defaultActionName = 'show';
    public $role = 'content';
    public $productsList = [];

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['galleries'] = 'numbersArray';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
    }
}