<?php

class roomElement extends structureElement
{
    public $dataResourceName = 'module_room';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['number'] = 'naturalNumber';
        $moduleStructure['originalName'] = 'fileName';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields = [
            'title',
        ];
    }

    public function getConnectedShop()
    {
        foreach ($this->getConnectedShopsIds() as $shopElementId) {
            return $this->getService('structureManager')->getElementById($shopElementId);
        }
        return null;
    }

    public function getConnectedShopsIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'shopRoom', 'child');
    }

    public function getFloor()
    {
        $result = null;
        $linksManager = $this->getService('linksManager');
        $connectedFloorsIds = $linksManager->getConnectedIdList($this->id, 'structure', 'child');
        foreach ($connectedFloorsIds as $floorId) {
            $result = $this->getService('structureManager')->getElementById($floorId);
            break;
        }
        return $result;
    }
}


