<?php

class roomsMapElement extends structureElement
{
    public $dataResourceName = 'module_roomsmap';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $categoriesList = [];
    public $role = 'container';
    const LINK_TYPE_FLOOR = 'roomsMapFloor';
    protected $categories;
    protected $icons;
    protected $floors;
    protected $shortestUrl;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
    }

    public function getCatalogue()
    {
        static $result;

        if ($result === null) {
            $result = false;
            $structureManager = $this->getService('structureManager');
            $languageId = $this->getService('LanguagesManager')->getCurrentLanguageId();
            foreach ($structureManager->getElementsByType('shopCatalogue', $languageId) as $shopCatalogue) {
                $result = $shopCatalogue;
                break;
            }
        }
        return $result;
    }

    public function getShopsAlfaIndex()
    {
        $result = [];
        if ($catalogue = $this->getCatalogue()) {
            $result = $catalogue->getShopsAlfaIndex();
        }
        return $result;
    }

    public function getInfo($deviceType = 'desktop')
    {
        $controller = controller::getInstance();
        $floorsInfo = [];
        $roomsInfo = [];
        $floorNumber = 0;
        $svgExportDir = ROOT_PATH . 'converter/';
        $svgExported = is_dir($svgExportDir);
        foreach ($this->getFloors() as $floorElement) {
            $svgExportFile = $svgExportDir . 'javascript.floorRooms' . $floorNumber . '.json';
            if ($svgExported && is_file($svgExportFile)) {
                $mapInfo = json_decode(file_get_contents($svgExportFile));
            } else {
                $mapInfo = $floorElement->getRoomMapInfo();
            }
            $floorsInfo[] = [
                'id' => $floorElement->id,
                'number' => $floorNumber,
                'title' => $floorElement->title,
                'mapInfo' => $mapInfo,
                'rooms' => [],
            ];
            foreach ($floorElement->getRooms() as $roomElement) {
                if ($roomElement) {
                    $shopId = 0;
                    if ($ids = $roomElement->getConnectedShopsIds()) {
                        $shopId = (int)$ids[0];
                    }
                    $roomsInfo[] = [
                        'title' => $roomElement->title,
                        'id' => $roomElement->id,
                        'number' => $roomElement->number,
                        'floorId' => $floorElement->id,
                        'floorNumber' => $floorNumber,
                        'shopId' => $shopId,
                    ];
                }
            }
            ++$floorNumber;
        }
        $iconsInfo = [];
        foreach ($this->getIcons() as $iconElement) {
            $iconInfo = [
                'id' => $iconElement->id,
                'title' => $iconElement->title,
            ];
            if ($iconElement->originalName) {
                $iconInfo['image'] = $controller->baseURL . 'image/type:roomsMapIcon/id:' . $iconElement->image . '/filename:' . $iconElement->originalName;
            }
            $iconsInfo[] = $iconInfo;
        }

        $categoriesInfo = [];
        $shopsInfo = [];
        foreach ($this->getCategories() as $category) {
            $categoriesInfo[] = [
                'id' => $category->id,
                'title' => $category->title,
                'color' => $category->color,
            ];
            foreach ($category->getShopsList() as $shop) {
                $shopInfo = [
                    'categoryId' => $category->id,
                    'id' => $shop->id,
                    'title' => $shop->title,
                    'introduction' => $shop->introduction,
                    'openedTime' => $shop->openedTime,
                    'openingHoursInfo' => $shop->getOpeningHoursInfo(),
                    'contactInfo' => $shop->contactInfo,
                    'URL' => $shop->URL,
                    'roomsIds' => $shop->getConnectedRoomsIds(),
                    'image' => '',
                    'logo' => '',
                ];
                if ($shop->originalName) {
                    $shopInfo['logo'] = $controller->baseURL . 'image/type:shopShortLogo/id:' . $shop->image . '/filename:' . $shop->originalName;
                }
                if ($shop->photoOriginalName) {
                    $shopInfo['image'] = $controller->baseURL . 'image/type:shopShortPhoto/id:' . $shop->photo . '/filename:' . $shop->photoOriginalName;
                }
                $campaignsInfo = [];
                foreach ($shop->getCampaigns() as $campaign) {
                    $campaignInfo = [
                        'title' => $campaign->title,
                        'content' => $campaign->introduction,
                    ];
                    if ($campaign->originalName) {
                        $campaignInfo['image'] = $controller->baseURL . 'image/type:campaignBar/id:' . $campaign->image . '/filename:' . $campaign->originalName;
                    }
                    $campaignsInfo[] = $campaignInfo;
                }
                $shopInfo['campaigns'] = $campaignsInfo;
                $shopsInfo[] = $shopInfo;
            }
        }
        $mapUrl = $this->URL;
        // if parent contains only me, use parent URL
        $parentElement = $this->getService('structureManager')->getElementsFirstParent($this->id);
        if ($parentElement && !$parentElement->getChildrenList('content')) {
            $mapUrl = $parentElement->URL;
        }
        return [
            'categories' => $categoriesInfo,
            'rooms' => $roomsInfo,
            'shops' => $shopsInfo,
            'floors' => $floorsInfo,
            'icons' => $iconsInfo,
            'mapUrl' => $mapUrl,
        ];
    }

    public function getCategories()
    {
        if ($this->categories === null) {
            $structureManager = $this->getService('structureManager');
            $languageId = $this->getService('LanguagesManager')->getCurrentLanguageId();
            foreach ($structureManager->getElementsByType('shopCatalogue', $languageId) as $shopCatalogue) {
                $this->categories = $shopCatalogue->getCategoriesList();
                break;
            }
        }
        return $this->categories;
    }

    public function getIcons()
    {
        if ($this->icons === null) {
            $this->icons = [];
            $structureManager = $this->getService('structureManager');
            $iconsElementId = $structureManager->getElementIdByMarker('icons');
            if ($iconsElementId) {
                $iconsElementsIds = $this->getService('linksManager')
                    ->getConnectedIdList($iconsElementId, 'structure', 'parent');
                if ($iconsElementsIds) {
                    $structureManager->getElementsByIdList($iconsElementsIds, $this->id);
                    foreach ($iconsElementsIds as $iconElementId) {
                        if ($element = $structureManager->getElementById($iconElementId)) {
                            $this->icons[] = $element;
                        }
                    }
                }
            }
        }
        return $this->icons;
    }

    public function getFloors()
    {
        if ($this->floors === null) {
            $this->floors = [];
            $structureManager = $this->getService('structureManager');
            $this->floors = $structureManager->getElementsChildren($this->id, 'content', self::LINK_TYPE_FLOOR);
        }
        return $this->floors;
    }

    public function getShortestUrl()
    {
        if ($this->shortestUrl === null) {
            $this->shortestUrl = $this->URL;
            $structureManager = $this->getService('structureManager');
            $parentElement = $structureManager->getElementsFirstParent($this->id);
            if ($parentElement && count($parentElement->getChildrenList()) == 1) {
                $this->shortestUrl = $parentElement->URL;
            }
        }
        return $this->shortestUrl;
    }
}


