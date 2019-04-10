<?php

class categoriesElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['category'];
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

    public function getFullChildrenList($roles = null)
    {
        $childrenList = [];
        $structureManager = $this->getService('structureManager');

        if ($children = $structureManager->getElementsChildren($this->id, 'container')) {
            $childrenList = $structureManager->getElementsFlatTree($this->id);
        }

        return $childrenList;
    }
}