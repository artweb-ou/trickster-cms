<?php

class bannerCategoryElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_banner_category';
    protected $allowedTypes = ['banner'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $banners;
    protected $bannersToDisplay;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['limit'] = 'text';
    }

    public function getBanners()
    {
        if (is_null($this->banners)) {
            $this->banners = [];
            $linksManager = $this->getService('linksManager');
            if ($bannersIds = $linksManager->getConnectedIdList($this->id, "bannerCategoryBanner", "parent")) {
                $structureManager = $this->getService('structureManager');
                foreach ($bannersIds as &$bannerId) {
                    if ($element = $structureManager->getElementById($bannerId)) {
                        $this->banners[] = $element;
                    }
                }
            }
        }
        return $this->banners;
    }

    public function getBannersToDisplay()
    {
        if (is_null($this->bannersToDisplay)) {
            $this->bannersToDisplay = [];
            $linksManager = $this->getService('linksManager');

            if ($connectedBannersIds = $linksManager->getConnectedIdList($this->id, "bannerCategoryBanner", "parent")) {
                $bannersCollection = persistableCollection::getInstance('module_banner');

                $conditions = [];
                $conditions[] = [
                    "column" => "id",
                    "action" => "in",
                    "argument" => $connectedBannersIds,
                ];
                $order = [];

                if (count($connectedBannersIds) > $this->limit) {
                    $order = ['rand'];
                }
                if ($this->limit == 0) {
                    $limit = null;
                } else {
                    $limit = $this->limit;
                }

                if ($queryResult = $bannersCollection->conditionalLoad('distinct(id)', $conditions, $order, $limit, [], true)
                ) {
                    $idList = [];
                    foreach ($queryResult as &$row) {
                        $idList[] = $row['id'];
                    }
                    $structureManager = $this->getService('structureManager');
                    if ($bannerElements = $structureManager->getElementsByIdList($idList, $this->id, "bannerCategoryBanner")
                    ) {
                        $positions = [];
                        $position = 0;
                        foreach ($connectedBannersIds as &$connectedBannersId) {
                            $positions[$connectedBannersId] = $position;
                            $position++;
                        }
                        foreach ($bannerElements as &$bannerElement) {
                            $sortData[] = $positions[$bannerElement->id];
                        }
                        array_multisort($sortData, SORT_ASC, $bannerElements);
                        $this->bannersToDisplay = $bannerElements;
                    }
                }
            }
        }
        return $this->bannersToDisplay;
    }

    public function getTemplate($viewName = null)
    {
        if (!is_null($viewName)) {
            if ($viewName == "footer") {
                $viewName = "widget";
            }
            return $this->structureType . '.' . $viewName . '.tpl';
        }

        if (is_null($this->template)) {
            $this->template = $this->structureType . '.' . $this->viewName . '.tpl';
        }
        return $this->template;
    }
}

