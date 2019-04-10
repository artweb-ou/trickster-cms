<?php

class receiveSelectedEvents extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->displayLimit = (int)$structureElement->displayLimit;
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();
            $structureElement->connectFormEvents();
            $structureElement->connectFormEventsLists();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'pastEventsHidden',
            'mode',
            'displayLimit',
            'connectedEvents',
            'connectedEventsLists',
            'displayMenus',
            'receivedEventsListsIds',
            'receivedEventsIds',
            'enableFilter',
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