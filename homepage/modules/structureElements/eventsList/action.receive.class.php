<?php

class receiveEventsList extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param eventsListElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->months = (int)$structureElement->months;
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();
            $structureElement->connectFormEvents();

            // connect all events if this is automatic events list
            if ($structureElement->mode == 'auto') {
                if ($events = $structureManager->getElementsByType('event')) {
                    $linksManager = $this->getService('linksManager');
                    foreach ($events as &$event) {
                        $linksManager->linkElements($structureElement->id, $event->id, 'eventsListEvent');
                    }
                }
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'months',
            'enableFilter',
            'period',
            'startMonth',
            'startYear',
            'endMonth',
            'endYear',
            'displayMenus',
            'structureRole',
            'receivedEventsIds',
            'mode',
            'dates_type',
            'date_from',
            'date_to',
            'sort',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}