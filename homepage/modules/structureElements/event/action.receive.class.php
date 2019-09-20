<?php

class receiveEvent extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param structureElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            /**
             * @var $structureElement structureElement
             */
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $additionalImages = [
                2=>'image2',
            ];
            foreach($additionalImages as $imageKey=>$imageCode) {
                if (!is_null($structureElement->getDataChunk($imageCode)->originalName)) {
                    $structureElement->$imageCode = $structureElement->id . "_$imageKey";
                    $field = $imageCode . 'Name';
                    $structureElement->$field = $structureElement->getDataChunk($imageCode)->originalName;
                }
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
            'image2',
            'startDate',
            'endDate',
            'startTime',
            'endTime',
            'location',
            'country',
            'city',
            'address',
            'mapCode',
            'mapUrl',
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


