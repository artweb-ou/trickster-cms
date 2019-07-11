<?php

class eventsListElement extends menuDependantStructureElement implements ConfigurableLayoutsProviderInterface, EventsListFilterInterface
{
    use ConfigurableLayoutsProviderTrait, ConnectedEventsProviderTrait, EventsListFilterTrait;
    public $dataResourceName = 'module_eventslist';
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $events;
    protected $eventsIdList;
    protected $filteredEvents;
    protected $eventsGroupsIndex;
    protected $baseEventsIdList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['months'] = 'text';
        $moduleStructure['layout'] = 'text'; // list || short || calendar
        $moduleStructure['listLayout'] = 'text';
        $moduleStructure['enableFilter'] = 'checkbox';
        $moduleStructure['period'] = 'text'; // auto || custom
        $moduleStructure['startMonth'] = 'text';
        $moduleStructure['startYear'] = 'text';
        $moduleStructure['endMonth'] = 'text';
        $moduleStructure['endYear'] = 'text';
        $moduleStructure['mode'] = 'text';
        $moduleStructure['receivedEventsIds'] = 'array'; // temporary

        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';

        $moduleStructure['dates_type'] = 'text';
        $moduleStructure['date_from'] = 'date';
        $moduleStructure['date_to'] = 'date';
        $moduleStructure['sort'] = 'text';

        $moduleStructure['colorLayout'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showFullList',
            'showForm',
            'showSeoForm',
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
                $query->whereIn('id', $this->getConnectedEventsIds());
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

    /**
     * @return array
     * @deprecated
     */
    public function getEvents()
    {
        $this->logError('deprecated method getEventsElements used');
        return $this->getEventsElements();
    }

    /**
     * @return array
     * @deprecated
     */
    public function getEventsToDisplay()
    {
        $this->logError('deprecated method getEventsToDisplay used');
        return $this->getEventsElements();
    }

    /**
     * @param $monthStartStamp
     * @return array
     *
     * @deprecated
     */
    public function getFilteredEvents($monthStartStamp)
    {
        $this->logError('deprecated method getFilteredEvents used');
        if (is_null($this->filteredEvents)) {
            $this->filteredEvents = [];
            if ($events = $this->getEvents()) {
                $selectedMonth = date('n', $monthStartStamp);
                $selectedYear = date('Y', $monthStartStamp);
                foreach ($events as $key => &$event) {
                    $eventStartStamp = strtotime('first day of this month ' . $event->startDate);
                    if (date('n', $eventStartStamp) != $selectedMonth || date('Y', $eventStartStamp) != $selectedYear
                    ) {
                        unset($events[$key]);
                    }
                }
                $this->filteredEvents = $events;
            }
        }
        return $this->filteredEvents;
    }
}


