<?php

class selectedEventsElement extends menuDependantStructureElement implements ConfigurableLayoutsProviderInterface, EventsListFilterInterface
{
    use ConfigurableLayoutsProviderTrait;
    use ConnectedEventsProviderTrait;
    use EventsListFilterTrait;
    use SearchTypesProviderTrait;

    public $dataResourceName = 'module_selectedevents';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $eventsToDisplay;
    protected $baseEventsIdList;
    public $connectedMenu;
    protected $fixedElement;
    protected $allowedTypes = [];


    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['pastEventsHidden'] = 'checkbox';
        $moduleStructure['displayLimit'] = 'text';
        $moduleStructure['mode'] = 'text';
        $moduleStructure['connectedEvents'] = 'numbersArray';
        $moduleStructure['connectedEventsLists'] = 'numbersArray';

        $moduleStructure['layout'] = 'text';
        $moduleStructure['listLayout'] = 'text';
        $moduleStructure['enableFilter'] = 'checkbox';
        $moduleStructure['gotoButtonTitle'] = 'text';
        $moduleStructure['fixedId'] = 'text';

        $moduleStructure['dates_type'] = 'text';
        $moduleStructure['date_from'] = 'date';
        $moduleStructure['date_to'] = 'date';
        $moduleStructure['sort'] = 'text';

        $moduleStructure['receivedEventsIds'] = 'array'; // temporary
        $moduleStructure['receivedEventsListsIds'] = 'array'; // temporary

        $moduleStructure['colorLayout'] = 'text';
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

    protected function getBaseEventsIdList()
    {
        if ($this->baseEventsIdList === null) {
            $this->baseEventsIdList = [];
            $db = $this->getService('db');

            $query = $db->table('module_event')->select('id')->distinct();
            if ($this->mode == 'custom') {
                $eventIds = array_merge($this->getConnectedEventsIds(), $this->getEventsListsEventsIds());
                $query->whereIn('id', $eventIds);
            }
            switch ($this->dates_type) {
                case 'past_events':
                    $todayStamp = strtotime("today 00:00");
                    $query->where('startDate', '<', $todayStamp);
                    $query->where('endDate', '<', $todayStamp);
                    break;
                case 'future_events':
                    $todayStamp = strtotime("today 00:00");
                    $query->where(function ($query) use ($todayStamp) {
                        $query->where('endDate', '>=', $todayStamp);
                        $query->orWhere('startDate', '>=', $todayStamp);
                    });
                    break;
                default:
                    break;
            }

            if ($this->date_from) {
                $query->where('startDate', '>', strtotime($this->date_from));
            }
            if ($this->date_to) {
                $query->where('startDate', '<', strtotime($this->date_to));
                $query->where('endDate', '<', strtotime($this->date_to));
            }

            if ($result = $query->get()) {
                $this->baseEventsIdList = array_column($result, 'id');
            }
        }
        return $this->baseEventsIdList;
    }


    public function getEventsListsEventsIds()
    {
        $linksManager = $this->getService('linksManager');
        $eventIds = [];
        foreach ($this->getConnectedEventsListsIds() as $connectedEventsListId) {
            $eventsListEventIds = $linksManager->getConnectedIdList($connectedEventsListId, 'eventsListEvent', 'parent');
            foreach ($eventsListEventIds as $eventId) {
                $eventIds[] = $eventId;
            }
        }
        return $eventIds;
    }

    public function getConnectedEventsListsInfo()
    {
        $info = [];
        foreach ($this->getConnectedEventsLists() as $element) {
            $item['title'] = $element->getTitle();
            $item['select'] = true;
            $item['id'] = $element->id;

            $info [] = $item;
        }
        return $info;
    }

    public function getConnectedEventsInfo()
    {
        $info = [];
        foreach ($this->getConnectedEvents() as $element) {
            $item['title'] = $element->getTitle();
            $item['select'] = true;
            $item['id'] = $element->id;

            $info [] = $item;
        }
        return $info;
    }

    public function getEventsElements()
    {
        if ($this->t_events === null) {
            if ($eventIds = $this->getCurrentEventsIdList()) {
                //we need to ensure all connected events lists are loaded.
                //Then events will be taken through the right events list, not from anywhere in language
                $this->getConnectedEventsLists();
                /**
                 * @var structureManager
                 */
                $structureManager = $this->getService('structureManager');
                foreach ($eventIds as $eventId) {
                    if ($element = $structureManager->getElementById($eventId)) {
                        $this->t_events[] = $element;
                    }
                }
            }
        }
        return $this->t_events;
    }



    public function getFixedElement()
    {
        if ($this->fixedElement === null && $this->fixedId) {
            $structureManager = $this->getService('structureManager');
            $this->fixedElement = $structureManager->getElementById($this->fixedId);
        }
        return $this->fixedElement;
    }

    public function getFixedElementURL()
    {
        if ($fixedElement = $this->getFixedElement()) {
            return $fixedElement->URL;
        }
        return false;
    }

    public function gotoButtonTitle()
    {
        if ($gotoButtonTitle = $this->gotoButtonTitle) {
            return $gotoButtonTitle;
        }
        else {
            $translationsManager = $this->getService('translationsManager');
            return $translationsManager->getTranslationByName('events.look_calendar');
        }
    }

    public function getAllowedTypes($currentAction = 'showForm')
    {
        return $this->allowedTypes;
    }

}