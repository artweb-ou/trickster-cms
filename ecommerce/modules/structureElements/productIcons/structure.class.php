<?php

class productIconsElement extends structureElement
{
    use AutoMarkerTrait;

    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['genericIcon'];
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

    public function getChildrenList($roles = null, $linkType = 'structure', $allowedTypes = null, $restrictLinkTypes = false)
    {
        $structureManager = $this->getService('structureManager');
        //is it possible that we should always use blacklist when loading children?
        $childrenList = $structureManager->getElementsChildren($this->id);

        return $childrenList;
    }
}