<?php

class brandsElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['brand'];
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
        if (is_null($this->contentList)) {
            $this->contentList = false;
            $structureManager = $this->getService('structureManager');

            if ($children = $structureManager->getElementsChildren($this->id)) {
                $this->contentList = $children;
                $sortParameter = [];
                foreach ($this->contentList as &$element) {
                    $sortParameter[] = mb_strtolower($element->title);
                }

                array_multisort($sortParameter, SORT_ASC, $this->contentList);
            }
        }
        return $this->contentList;
    }
}
