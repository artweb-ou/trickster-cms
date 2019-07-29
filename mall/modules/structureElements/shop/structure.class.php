<?php

class shopElement extends structureElement implements MetadataProviderInterface
{
    use GalleryInfoProviderTrait;
    use MetadataProviderTrait {
        getTextContent as getTextContentTrait;
    }
    public $dataResourceName = 'module_shop';
    protected $allowedTypes = ['galleryImage'];
    public $defaultActionName = 'show';
    public $role = 'content';
    public $roomsMapURL = '';
    public $floor = 1;
    public $number = 0;
    public $campaignsList = [];
    protected $requestedParentCategory = null;
    protected $connectedRooms;
    protected $campaigns;
    const LINK_TYPE_CATEGORY = 'shopCategory';
    const LINK_TYPE_ROOM = 'shopRoom';
    const LINK_TYPE_OPENING_HOURS = 'shopOpeningHours';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['content'] = 'html';
        $moduleStructure['openedTime'] = 'html';
        $moduleStructure['contactInfo'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['photo'] = 'image';
        $moduleStructure['photoOriginalName'] = 'fileName';
        $moduleStructure['roomId'] = 'serializedArray';
        $moduleStructure['customOpeningHours'] = 'jsonSerialized';

        $moduleStructure['categories'] = 'numbersArray';
        $moduleStructure['campaigns'] = 'numbersArray';
        $moduleStructure['rooms'] = 'numbersArray';
        $moduleStructure['openingHoursGroupId'] = 'text';

        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'content';
        $multiLanguageFields[] = 'introduction';
        $multiLanguageFields[] = 'openedTime';
        $multiLanguageFields[] = 'contactInfo';
        $multiLanguageFields[] = 'metaTitle';
        $multiLanguageFields[] = 'h1';
        $multiLanguageFields[] = 'metaDescription';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showTexts',
            'showSeoForm',
            'showImagesForm',
            'showPrivileges',
        ];
    }

    public function getConnectedRooms()
    {
        if ($this->connectedRooms === null) {
            $this->connectedRooms = $this->getService('structureManager')
                ->getElementsChildren($this->id, null, self::LINK_TYPE_ROOM);
        }
        return $this->connectedRooms;
    }

    public function getFloor()
    {
        $result = null;
        foreach ($this->getConnectedRooms() as $room) {
            $result = $room->getFloor();
            break;
        }
        return $result;
    }

    public function getMainCategory()
    {
        $result = null;
        foreach ($this->getConnectedCategories() as $category) {
            $result = $category;
            break;
        }
        return $result;
    }

    public function getConnectedRoomsIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, self::LINK_TYPE_ROOM, 'parent');
    }

    public function getConnectedCategories($forceUpdate = false)
    {
        $structureManager = $this->getService('structureManager');

        $categories = [];
        if ($parentsList = $structureManager->getElementsParents($this->id, $forceUpdate, 'shopCategory')) {
            foreach ($parentsList as &$parentElement) {
                if ($parentElement->structureType == 'shopCategory') {
                    $categories[] = $parentElement;
                }
            }
        }
        return $categories;
    }

    public function getRequestedParentCategory()
    {
        if (is_null($this->requestedParentCategory)) {
            $this->requestedParentCategory = false;
            $structureManager = $this->getService('structureManager');
            if ($parentsList = $structureManager->getElementsParents($this->id)) {
                foreach ($parentsList as &$parentElement) {
                    if ($parentElement->requested) {
                        $this->requestedParentCategory = $parentElement;
                        break;
                    } else {
                        if (!$this->requestedParentCategory) {
                            $this->requestedParentCategory = $parentElement;
                        }
                    }
                }
            }
        }
        return $this->requestedParentCategory;
    }

    public function getGalleryJsonInfo(
        $displaySelector = true,
        $displayTitle = true,
        $fullScreenGallery = true,
        $heightLogics = 'viewport',
        $imageResizeLogics = "resize",
        $changeDelay = 6000,
        $height = 0,
        $enableImagesButtons = false,
        $enablePlaybackButton = false,
        $descriptionType = 'overlay',
        $deviceType = "desktop",
        $bigImagePreset = "galleryImage",
        $enablePrevNextImagesButtons = false
    ) {
        $data = [
            'id' => $this->id,
            'displaySelector' => $displaySelector,
            'displayTitle' => $displayTitle,
            'fullScreenGallery' => $fullScreenGallery,
            'heightLogics' => $heightLogics,
            'imageResizeLogics' => $imageResizeLogics,
            'changeDelay' => $changeDelay,
            'enableImagesButtons' => $enableImagesButtons,
            'enablePlaybackButton' => $enablePlaybackButton,
            'descriptionType' => $descriptionType,
            'height' => $height,
            'enablePrevNextImagesButtons' => $enablePrevNextImagesButtons,
        ];
        $data['images'] = [];
        $controller = controller::getInstance();
        foreach ($this->getImagesList() as $imageElement) {
            $data['images'][] = [
                'fullImageUrl' => $controller->baseURL . 'image/type:galleryFullImage/id:' . $imageElement->image . '/filename:' . $imageElement->originalName,
                'bigImageUrl' => $controller->baseURL . 'image/type:' . $bigImagePreset . '/id:' . $imageElement->image . '/filename:' . $imageElement->originalName,
                'thumbnailImageUrl' => $controller->baseURL . 'image/type:gallerySmallThumbnailImage/id:' . $imageElement->image . '/filename:' . $imageElement->originalName,
                'title' => $imageElement->title,
                'description' => $imageElement->description,
                'alt' => $imageElement->alt,
                'link' => $imageElement->link,
                'externalLink' => $imageElement->externalLink,
                'id' => $imageElement->id,
            ];
        }
        return json_encode($data);
    }

    public function getImagesList()
    {
        $structureManager = $this->getService('structureManager');
        if (is_null($this->imagesList)) {
            $this->imagesList = [];
            $structureManager->getElementsChildren($this->id);

            foreach ($this->getChildrenList() as $childElement) {
                if ($childElement->structureType == 'galleryImage') {
                    $this->imagesList[] = $childElement;
                }
            }
        }
        return $this->imagesList;
    }

    public function getCampaigns()
    {
        if ($this->campaigns === null) {
            $this->campaigns = [];
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            $connectedIds = $linksManager->getConnectedIdList($this->id, 'campaigns', 'child');
            $connectedIds = array_reverse($connectedIds);
            $time = $_SERVER['REQUEST_TIME'];
            foreach ($connectedIds as $connectedId) {
                if ($element = $structureManager->getElementById($connectedId)) {
                    $startDateTime = $element->getDataChunk('startDate')->getStorageValue();
                    $endDateTime = $element->getDataChunk('endDate')->getStorageValue();
                    if (($startDateTime && $startDateTime > $time) || ($endDateTime && $endDateTime <= $time)) {
                        continue;
                    }
                    $this->campaigns[] = $element;
                }
            }
        }
        return $this->campaigns;
    }

    public function getFirstCampaign()
    {
        $result = null;
        foreach ($this->getCampaigns() as $result) {
            break;
        }
        return $result;
    }

    public function getMapUrl()
    {
        $result = '';
        $languagesManager = $this->getService('LanguagesManager');
        $structureManager = $this->getService('structureManager');

        $roomsIds = $this->getConnectedRoomsIds();
        $roomsMaps = [];
        if ($roomsIds) {
            $roomsMaps = $structureManager->getElementsByType('roomsMap', $languagesManager->getCurrentLanguageId());
        }
        foreach ($roomsMaps as $roomsMap) {
            $result = $roomsMap->getShortestUrl();
            $floorNumber = 0;
            $result .= '#view=plan&room=' . reset($roomsIds);
            foreach ($this->getConnectedRooms() as $room) {
                $shopFloor = $room->getFloor();
                $allFloors = $roomsMap->getFloors();
                $i = 0;
                foreach ($allFloors as $floor) {
                    if ($shopFloor->id == $floor->id) {
                        $floorNumber = $i;
                        break;
                    }
                    ++$i;
                }
                break;
            }
            $result .= '&floor=' . $floorNumber;
            break;
        }
        return $result;
    }

    public function getConnectedOpeningHoursGroupId()
    {
        $result = 0;
        $linksManager = $this->getService('linksManager');
        foreach ($linksManager->getConnectedIdList($this->id, self::LINK_TYPE_OPENING_HOURS, "parent") as $result) {
            break;
        }
        return $result;
    }

    public function getConnectedOpeningHoursGroup()
    {
        $result = null;
        $structureManager = $this->getService('structureManager');
        $openingHoursGroupId = $this->getConnectedOpeningHoursGroupId();
        if ($openingHoursGroupId) {
            $result = $structureManager->getElementById($openingHoursGroupId, $this->id, true);
        }
        return $result;
    }

    public function getOpeningHoursInfo()
    {
        // TODO: make a new class/trait. this method is duplicated in shop and openingHoursGroup
        $result = [];
        $daysHours = [];
        if ($this->customOpeningHours) {
            $daysHours = $this->customOpeningHours;
        } elseif ($group = $this->getConnectedOpeningHoursGroup()) {
            $daysHours = $group->hoursData;
        }
        if (count($daysHours) == 7) {
            $translationsManager = $this->getService('translationsManager');
            for ($i = 0; $i < 7; ++$i) {
                $dayInfo = $daysHours[$i];
                $periodName = $translationsManager->getTranslationByName('calendar.weekday_abbreviation_'
                    . ($i + 1), null, true);
                $dayClosed = !empty($dayInfo['closed']) || (!$dayInfo['start'] && !$dayInfo['end']);
                if ($dayClosed) {
                    $periodTimes = $translationsManager->getTranslationByName('openinghoursinfo.closed', null, true);
                } else {
                    $periodTimes = $dayInfo['start'] . '-' . $dayInfo['end'];
                }
                $endDayNumber = $i;
                for ($j = $i + 1; $j < 7; ++$j) {
                    $nextDayInfo = $daysHours[$j];
                    $nextDayClosed = !empty($nextDayInfo['closed']) || (!$nextDayInfo['start'] && $nextDayInfo['end']);
                    if ($nextDayClosed && $dayClosed || ($nextDayClosed == $dayClosed && $nextDayInfo['start'] == $dayInfo['start']
                            && $nextDayInfo['end'] == $dayInfo['end'])
                    ) {
                        $endDayNumber = $j;
                    } else {
                        break;
                    }
                }
                if ($endDayNumber != $i) {
                    $periodName .= '-' . $translationsManager->getTranslationByName('calendar.weekday_abbreviation_'
                            . ($endDayNumber + 1), null, true);
                    $i = $endDayNumber;
                }
                $result[] = [
                    'name' => $periodName,
                    'times' => $periodTimes,
                ];
            }
        }
        return $result;
    }

    public function getTextContent()
    {
        if (is_null($this->textContent)) {
            $this->textContent = $this->getTextContentTrait();

            if ($this->final) {
                $category = $this->getRequestedParentCategory();
                if ($categoryMetaDescriptionTemplate = $category->metaDescriptionTemplate) {
                    preg_match_all("|{(.*)}|sUi", $categoryMetaDescriptionTemplate, $results);
                    $search = [];
                    $replace = [];
                    foreach ($results[1] as $result) {
                        $search[] = '{' . $result . '}';
                        switch ($result) {
                            case 'title':
                                {
                                    $replace[] = $this->title;
                                }
                                break;
                            default:
                                $replace[] = '';
                        }
                    }

                    $this->textContent = str_replace($search, $replace, $categoryMetaDescriptionTemplate);
                }
            }
        }
        return $this->textContent;
    }

    public function getTemplatedMetaTitle()
    {
        if ($this->final) {
            $category = $this->getRequestedParentCategory();
            if ($categoryMetaTitleTemplate = $category->metaTitleTemplate) {
                preg_match_all("|{(.*)}|sUi", $categoryMetaTitleTemplate, $results);
                $search = [];
                $replace = [];
                foreach ($results[1] as $result) {
                    $search[] = '{' . $result . '}';
                    switch ($result) {
                        case 'title':
                            {
                                $replace[] = $this->title;
                            }
                            break;
                        default:
                            $replace[] = '';
                    }
                }

                return str_replace($search, $replace, $categoryMetaTitleTemplate);
            }
        }

        return '';
    }

    public function getTemplatedH1()
    {
        if ($this->final) {
            $category = $this->getRequestedParentCategory();
            if ($categoryMetaH1Template = $category->metaH1Template) {
                preg_match_all("|{(.*)}|sUi", $categoryMetaH1Template, $results);
                $search = [];
                $replace = [];
                foreach ($results[1] as $result) {
                    $search[] = '{' . $result . '}';
                    switch ($result) {
                        case 'title':
                            {
                                $replace[] = $this->title;
                            }
                            break;
                        default:
                            $replace[] = '';
                    }
                }

                return str_replace($search, $replace, $categoryMetaH1Template);
            }
        }

        return '';
    }
}


