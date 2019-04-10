<?php

class floorElement extends structureElement
{
    use SortedChildrenListTrait;
    public $dataResourceName = 'module_floor';
    protected $allowedTypes = ['room'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $rooms;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['nodesInfo'] = 'structure';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields = [
            'title',
        ];
    }

    public function getRoomMapInfo()
    {
        $result = [];
        $rooms = [];
        $nodesInfo = $this->getNodesInfo();
        if (!empty($nodesInfo['base'])) {
            $points = [];
            foreach ($nodesInfo['base'] as $coord) {
                $points[] = 'L ' . round($coord['x']) . ',' . round($coord['y']);
            }
            $points[] = $points[0];
            $points[0] = str_replace('L', 'M', $points[0]);
            $path = implode('  ', $points);
            $rooms[] = [
                'id' => 'background',
                'walls' => ['top' => [$path]],
            ];
        }
        if (!empty($nodesInfo['room'])) {
            foreach ($nodesInfo['room'] as $roomId => $roomCoords) {
                $points = [];
                foreach ($roomCoords as $coord) {
                    $points[] = 'L ' . round($coord['x']) . ',' . round($coord['y']);
                }
                $points[] = $points[0];
                $points[0] = str_replace('L', 'M', $points[0]);
                $path = implode('  ', $points);
                $rooms[] = [
                    'id' => $roomId,
                    'walls' => ['top' => [$path]],
                ];
            }
        }
        $result['rooms'] = $rooms;
        $icons = [];
        if (!empty($nodesInfo['icon'])) {
            foreach ($nodesInfo['icon'] as $id => $iconsList) {
                foreach ($iconsList as $icon) {
                    $icon = [
                        'code' => $id,
                        'x' => round($icon['x']),
                        'y' => round($icon['y']),
                        'width' => $icon['width'],
                        'height' => $icon['height'],
                        'rotation' => isset($icon['rotation']) ? $icon['rotation'] : '',
                    ];
                    $icons[] = $icon;
                }
            }
        }
        $result['icons'] = $icons;
        return $result;
    }

    public function getNodesInfo()
    {
        $nodesInfo = [];
        if ($this->nodesInfo) {
            $nodesInfo = json_decode($this->nodesInfo, true);
        }
        return $nodesInfo;
    }

    public function setNodesInfo($info)
    {
        $this->nodesInfo = json_encode($info);
        $this->persistModuleData();
    }

    public function getRooms()
    {
        if ($this->rooms === null) {
            $this->rooms = $this->getChildrenList();
        }
        return $this->rooms;
    }

    public function getEditorInfo()
    {
        $controller = controller::getInstance();
        $nodesInfo = $this->getNodesInfo();
        $result = [
            'rooms' => [],
            'icons' => [],
        ];
        $result['id'] = $this->id;
        $schemeImageUrl = '';
        if ($this->image) {
            $schemeImageUrl = "{$controller->baseURL}image/type:floorMapScheme/id:{$this->image}/filename:{$this->originalName}";
        }
        $result['image'] = $schemeImageUrl;
        $result['baseNodes'] = isset($nodesInfo['base']) ? $nodesInfo['base'] : [];
        $roomElements = $this->getRooms();
        if ($roomElements) {
            foreach ($roomElements as $roomElement) {
                $roomNodes = [];
                if (isset($nodesInfo['room']) && isset($nodesInfo['room'][$roomElement->id])) {
                    $roomNodes = $nodesInfo['room'][$roomElement->id];
                }
                $result['rooms'][] = [
                    'id' => $roomElement->id,
                    'title' => $roomElement->title,
                    'nodes' => $roomNodes,
                ];
            }
            $structureManager = $this->getService('structureManager');
            $iconElements = $structureManager->getElementsByType('icon');
            foreach ($iconElements as $iconElement) {
                $iconNodes = [];
                if (isset($nodesInfo['icon']) && isset($nodesInfo['icon'][$iconElement->id])) {
                    $iconNodes = $nodesInfo['icon'][$iconElement->id];
                }
                $iconImage = '';
                if ($iconElement->image) {
                    $iconImage = $controller->baseURL . 'image/type:adminImage/id:'
                        . $iconElement->image . '/filename:' . $iconElement->originalName;
                }
                $result['icons'][] = [
                    'id' => $iconElement->id,
                    'title' => $iconElement->title,
                    'image' => $iconImage,
                    'nodes' => $iconNodes,
                ];
            }
        }
        return $result;
    }
}


