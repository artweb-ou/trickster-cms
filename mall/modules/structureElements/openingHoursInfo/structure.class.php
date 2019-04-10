<?php

class openingHoursInfoElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_openinghours_info';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $groups;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['exceptional'] = 'checkbox';
        $moduleStructure['displayMenus'] = 'array';
    }

    public function getGroups()
    {
        if ($this->groups === null) {
            $this->groups = [];
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            $connectedIds = $linksManager->getConnectedIdList($this->id, 'openingHoursInfoGroup', 'parent');
            foreach ($connectedIds as $connectedId) {
                $this->groups[] = $structureManager->getElementById($connectedId);
            }
        }
        return $this->groups;
    }
}