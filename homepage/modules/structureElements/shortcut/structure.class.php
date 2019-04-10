<?php

class shortcutElement extends structureElement
{
    public $dataResourceName = 'module_shortcut';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $replacementElements;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['target'] = 'text';
    }

    public function setReplacementElements($list)
    {
        $this->replacementElements = $list;
    }

    public function getReplacementElements($roles)
    {
        return $this->replacementElements;
    }
}


