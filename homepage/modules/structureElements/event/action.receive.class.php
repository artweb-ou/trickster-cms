<?php

class receiveEvent extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }

            // Connect event to selected eventsLists
            $linksManager = $this->getService('linksManager');
            $connectedEventsListsIds = $structureElement->connectedEventsLists;
            $connectedEventsLists = $structureElement->getConnectedEventsLists();
            foreach ($connectedEventsLists as $connectedEventList) {
                if (!in_array($connectedEventList->id, $connectedEventsListsIds)) {
                    $linksManager->unLinkElements($connectedEventList->id, $structureElement->id, 'eventsListEvent');
                }
            }
            foreach ($connectedEventsListsIds as $connectedEventsListId) {
                $linksManager->linkElements($connectedEventsListId, $structureElement->id, 'eventsListEvent');
            }
            $structureElement->persistElementData();

            // connect all eventslists
            if ($eventsLists = $structureManager->getElementsByType('eventsList')) {
                foreach ($eventsLists as &$eventsList) {
                    if ($eventsList->mode !== 'custom') {
                        $linksManager->linkElements($eventsList->id, $structureElement->id, 'eventsListEvent');
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
            'description',
            'introduction',
            'image',
            'startDate',
            'endDate',
            'startTime',
            'endTime',
            'location',
            'country',
            'city',
            'address',
            'mapCode',
            'link',
            'connectedEventsLists',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
        $validators['startDate'][] = 'notEmpty';
    }
}


