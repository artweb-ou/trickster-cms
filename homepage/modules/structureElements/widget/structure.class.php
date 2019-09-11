<?php

class widgetElement extends menuDependantStructureElement
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_widget';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hideTitle'] = 'checkbox';
        $moduleStructure['content'] = 'html';
        $moduleStructure['code'] = 'code';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['colorLayout'] = 'text';
    }
}


