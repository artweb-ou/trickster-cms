<?php

class newsMailsAddressesElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['newsMailAddress'];
    public $defaultActionName = 'showFullList';
    public $role = 'container';
    protected $groups;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['delimiter'] = 'text';
        $moduleStructure['importFile'] = 'file';
        $moduleStructure['groupId'] = 'numbersArray';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function getChildrenList($roles = null, $linkType = 'structure', $allowedTypes = null, $restrictLinkTypes = false)
    {
        $structureManager = $this->getService('structureManager');

        if ($childrenList = $structureManager->getElementsChildren($this->id, 'content')) {
            $sortParameter = [];
            foreach ($childrenList as &$element) {
                $sortParameter[] = strtolower($element->email);
            }
            array_multisort($sortParameter, SORT_ASC, $childrenList);
        }

        return $childrenList;
    }

    public function getContentList($roles = null, $linkType = 'structure')
    {
        if (is_null($this->contentList)) {
            $collection = persistableCollection::getInstance('module_newsmailaddress');
            $this->contentList = [];
            if ($elementsCount = $collection->countElements('id', [], [], true)) {
                $controller = controller::getInstance();
                $page = (int)$controller->getParameter('page');
                $elementsOnPage = 20;
                $pager = new pager($controller->fullURL, $elementsCount, $elementsOnPage, $page, 'page');

                $limitFields = [
                    $pager->startElement,
                    $elementsOnPage,
                ];
                if ($records = $collection->conditionalLoad('distinct(id)', [], ['email' => 'asc'], $limitFields, [], true)
                ) {
                    $eventsIds = [];
                    foreach ($records as &$record) {
                        $eventsIds[] = $record['id'];
                    }
                    if ($this->contentList = $this->getService('structureManager')->getElementsByIdList($eventsIds, $this->id)) {
                        $sort = [];
                        foreach ($this->contentList as &$element) {
                            $sort[] = strtolower($element->email);
                        }
                        array_multisort($sort, SORT_ASC, $this->contentList);
                        $this->getService('renderer')->assign("pager", $pager);
                    }
                }
            }
        }
        return $this->contentList;
    }

    public function getGroups()
    {
        if ($this->groups === null) {
            $this->groups = [];
            $structureManager = $this->getService('structureManager');
            if ($groupsFolder = $structureManager->getElementByMarker('newsMailsGroups')) {
                $this->groups = $structureManager->getElementsChildren($groupsFolder->id, 'content');
            }
        }
        return $this->groups;
    }
}

