<?php

class personnelListElement extends structureElement
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_personnellist';
    protected $allowedTypes = ['personnel'];
    public $defaultActionName = 'show';
    public $role = 'content';
    public $linkItems = [];

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['layout'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getPersonnelList()
    {
        return $this->getChildrenList();
    }
}


