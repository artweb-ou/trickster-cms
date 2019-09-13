<?php

class subArticleElement extends structureElement
{
    public $dataResourceName = 'module_article';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hideTitle'] = 'checkbox';
        $moduleStructure['content'] = 'html';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image'] = 'image';
    }
}

