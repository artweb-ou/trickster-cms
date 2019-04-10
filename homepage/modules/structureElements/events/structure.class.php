<?php

class eventsElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['event'];
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

    public function getContentList($roles = null, $linkType = 'structure')
    {
        if (is_null($this->contentList)) {
            $tableName = 'module_event';
            $db = $this->getService('db');
            $query = $db->table($tableName);

            $this->contentList = [];
            if ($elementsCount = $query->count()) {
                $pagerURL = $this->URL;
                $elementsOnPage = 20;
                $page = (int)controller::getInstance()->getParameter('page');
                $pager = new pager($pagerURL, $elementsCount, $elementsOnPage, $page, 'page');

                $query = $db->table($tableName)->distinct()->select('id');

                if ($records = $query->skip($pager->startElement)->take($elementsOnPage)->get()) {
                    $eventsIds = [];
                    foreach ($records as &$record) {
                        $eventsIds[] = $record['id'];
                    }
                    if ($this->contentList = $this->getService('structureManager')
                        ->getElementsByIdList($eventsIds, $this->id)
                    ) {
                        $sort = [];
                        foreach ($this->contentList as &$element) {
                            $sort[] = strtotime($element->startDate);
                        }
                        array_multisort($sort, SORT_DESC, $this->contentList);
                        $this->getService('renderer')->assign("pager", $pager);
                    }
                }
            }
        }
        return $this->contentList;
    }
}