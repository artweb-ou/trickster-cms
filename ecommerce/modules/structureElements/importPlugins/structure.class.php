<?php

class importPluginsElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [
        'accImportPlugin',
        'elkoImportPlugin',
        'abcImportPlugin',
        'tdBalticImportPlugin',
        'alsoImportPlugin',
    ];
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

        if ($childrenList = $structureManager->getElementsChildren($this->id)) {
            $sortParameter = [];
            foreach ($childrenList as &$element) {
                $sortParameter[] = mb_strtolower($element->title);
            }
            array_multisort($sortParameter, SORT_ASC, $childrenList);
        }

        return $childrenList;
    }
}
