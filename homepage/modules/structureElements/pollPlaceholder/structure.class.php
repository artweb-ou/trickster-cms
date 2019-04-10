<?php

class pollPlaceholderElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_poll_placeholder';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $pollElement;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['pollId'] = 'text';
    }

    public function getPollElement()
    {
        if (is_null($this->pollElement)) {
            $structureManager = $this->getService('structureManager');
            $this->pollElement = $structureManager->getElementById($this->pollId);
        }
        return $this->pollElement;
    }
}


