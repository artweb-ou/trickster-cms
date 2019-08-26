<?php

/**
 * Class eventElement
 *
 * @property string $startDate
 * @property string $startTime
 * @property string $endDate
 * @property string $endTime
 * @property string $image
 * @property string $image2
 */
class eventElement extends structureElement implements MetadataProviderInterface, ImageUrlProviderInterface
{
    use MetadataProviderTrait;
    use ImageUrlProviderTrait;
    use GalleryInfoProviderTrait;
    use ImagesElementTrait;
    use CacheOperatingElement;
    public $dataResourceName = 'module_event';
    protected $allowedTypes = ['galleryImage'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $startDayStamp;
    protected $startDayNumber;
    protected $endDayStamp;
    protected $period;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['description'] = 'html';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['startDate'] = 'date';
        $moduleStructure['endDate'] = 'date';
        $moduleStructure['startTime'] = 'time';
        $moduleStructure['endTime'] = 'time';
        $moduleStructure['location'] = 'text';
        $moduleStructure['country'] = 'text';
        $moduleStructure['city'] = 'text';
        $moduleStructure['address'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image2'] = 'image';
        $moduleStructure['image2Name'] = 'fileName';
        $moduleStructure['mapCode'] = 'code';
        $moduleStructure['mapUrl'] = 'url';
        $moduleStructure['link'] = 'url';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        // tmp
        $moduleStructure['connectedEventsLists'] = 'numbersArray';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'description';
        $multiLanguageFields[] = 'introduction';
        $multiLanguageFields[] = 'location';
        $multiLanguageFields[] = 'country';
        $multiLanguageFields[] = 'city';
        $multiLanguageFields[] = 'address';
        $multiLanguageFields[] = 'metaTitle';
        $multiLanguageFields[] = 'h1';
        $multiLanguageFields[] = 'metaDescription';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
            'showImages',
        ];
    }

    public function getStartDayStamp()
    {
        if (is_null($this->startDayStamp)) {
            $this->startDayStamp = strtotime($this->startDate);
        }
        return $this->startDayStamp;
    }

    public function getEndDayStamp()
    {
        if (is_null($this->endDayStamp)) {
            $this->endDayStamp = strtotime($this->endDate);
        }
        return $this->endDayStamp;
    }

    public function getStartDayNumber()
    {
        if (is_null($this->startDayNumber)) {
            $this->startDayNumber = date('j', $this->getStartDayStamp());
        }
        return $this->startDayNumber;
    }

    public function getStartDate($format = false)
    {
        if ($format) {
            return date($format, $this->getStartDayStamp());
        }
        return $this->startDate;
    }

    public function getEndDate($format = false)
    {
        if ($format) {
            return date($format, $this->getEndDayStamp());
        }
        return $this->endDate;
    }

    public function getPeriod()
    {
        if ($this->period === null) {
            $period = $this->startDate;
            if ($this->startTime) {
                $period .= " " . $this->startTime;
            }
            if (($this->endDate && $this->startDate != $this->endDate) || $this->endTime) {
                $period .= " -";
                if ($this->endDate && $this->startDate != $this->endDate) {
                    $period .= " " . $this->endDate;
                }
                if ($this->endTime) {
                    $period .= " " . $this->endTime;
                }
            }
            $this->period = $period;
        }
        return $this->period;
    }

    public function getMachineReadableDateTime($date, $time)
    {
        return gmdate('Y-m-d\TH:i\Z', strtotime($date . " " . $time));
    }

    /**
     * @return eventsListElement[]
     */
    public function getConnectedEventsLists()
    {
        $structureManager = $this->getService('structureManager');
        $connectedEventsLists = $structureManager->getElementsParents($this->id, 'eventsListEvent');
        return $connectedEventsLists;
    }

    /**
     * @return eventsListElement[]
     */
    public function getConnectedEventsListsInfo()
    {
        /**
         * @var structureManager $structureManager
         */
        $structureManager = $this->getService('structureManager');
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');

        $connectedIdIndex = $linksManager->getConnectedIdIndex($this->id, 'eventsListEvent', 'child');
        $allEventsLists = $structureManager->getElementsByType('eventsList');
        $connectedEventsListsInfo = [];

        foreach ($allEventsLists as $eventsList) {
            $item['id'] = $eventsList->id;
            $item['title'] = $eventsList->getSearchTitle();
            $item['select'] = isset($connectedIdIndex[$eventsList->id]);
            $connectedEventsListsInfo[] = $item;
        }
        return $connectedEventsListsInfo;
    }

    public function getImagesLinkType()
    {
        //legacy-support, use trait's method instead
        return 'structure';
    }

    public function getJsonInfo($galleryOptions = [], $imagePresetBase = 'gallery')
    {
        return $this->getGalleryJsonInfo($galleryOptions, $imagePresetBase);
    }

    public function getImageId($mobile = false)
    {
        if ($this->image2) {
            return $this->image2;
        }
        return $this->image;
    }

    public function getEventById($id) {
        $structureManager = $this->getService('structureManager');
        if ($event = $structureManager->getElementById($id)) {
            return $event;
        }
        return false;
    }

    public function getSameEventsListsEvents($amount = 4)
    {
        $result = [];
        if ($eventsLists = $this->getConnectedEventsLists()) {
            $idList = [];
            foreach ($eventsLists as $eventsList) {
                $idList = array_merge($idList, $eventsList->getCurrentEventsIdList());
            }
            $idList = array_unique($idList);
            $eventExcludeIds = [];

            $currentDate = date_create();
            $currentTimestamp =  date_timestamp_get($currentDate);

            foreach ($idList as $eventKey => $eventId) {
                $event = $this->getEventById($eventId);

                if ($currentTimestamp > $event->getEndDayStamp() || $this->id === $eventId) {
                    $eventExcludeIds[] = $eventId;
                }
            }

            $idList = array_diff($idList, $eventExcludeIds);

            /**
             * @var structureManager $structureManager
             */
            $structureManager = $this->getService('structureManager');
            shuffle($idList);
            while ($amount) {
                if ($id = array_pop($idList)) {
                    if ($element = $structureManager->getElementById($id)) {
                        $result[] = $element;
                        $amount--;
                    }
                }
            }
        }
        return $result;
    }
}
