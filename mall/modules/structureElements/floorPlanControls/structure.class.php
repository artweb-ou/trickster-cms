<?php

class floorPlanControlsElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_generic';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }
}


