<?php

abstract class categoryStructureElement extends productsListStructureElement
{
    /**
     * @deprecated
     */
    protected $usedParametersIds;
    /**
     * @deprecated
     */
    protected $selectedUsedParameters;
    protected $parametersGroups;
    protected $sortingOptions;

    public function getParametersGroups()
    {
        if (is_null($this->parametersGroups)) {
            $this->loadParametersGroups();
        }
        return $this->parametersGroups;
    }

    protected function loadParametersGroups()
    {
        $this->parametersGroups = [];
        $structureManager = $this->getService('structureManager');

        //load list of all product parameter groups according to product parameters connected to this category.
        //this list should be sorted the same way as in admin page/shopping basket settings/parameter groups
        if ($idList = $this->getParametersIdList()) {
            /**
             * @var \Illuminate\Database\Connection $db
             */
            $db = $this->getService('db');
            $query = $db->table('structure_links as links1')
                ->select(['links1.parentStructureId'])
                ->distinct()
                ->where('links1.type', '=', 'structure')
                ->whereIn('links1.childStructureId', $idList)
                ->leftJoin('structure_links as links2', 'links2.childStructureId', '=', 'links1.parentStructureId')
                ->where('links2.type', '=', 'structure')
                ->orderBy('links2.position', 'asc');

            if ($records = $query->get()) {
                if ($groupsIdList = array_column($records, 'parentStructureId')) {
                    if ($elementsList = $structureManager->getElementsByIdList($groupsIdList, $this->id, 'idlist')
                    ) {
                        foreach ($elementsList as &$element) {
                            if ($element->structureType == 'productParametersGroup' && !$element->hidden) {
                                $this->parametersGroups[] = $element;
                            }
                        }
                    }
                }
            }
        }
    }

    public function isFilterable()
    {
        return $this->productsLayout !== 'hide' && parent::isFilterable();
    }

    public function isFilterableByBrand()
    {
        return $this->isSettingEnabled('brandFilterEnabled');
    }

    public function isFilterableByDiscount()
    {
        return $this->isSettingEnabled('discountFilterEnabled');
    }

    public function isFilterableByAvailability()
    {
        return ($this->isSettingEnabled('availabilityFilterEnabled'));
    }

    public function isFilterableByParameter()
    {
        return ($this->isSettingEnabled('parameterFilterEnabled') && $this->getFilterSelections());
    }

    /**
     * @return array
     *
     * @deprecated since 31.10.16
     */
    public function getSortParameters()
    {
        $this->logError('Deprecated method getSortParameters used, use getSortingOptions');
        if (is_null($this->sortParameters)) {
            $arguments = $this->parseSearchArguments();
            $filteredUrl = $this->getFilteredUrl();
            $this->sortParameters = [];
            if ($this->isSettingEnabled("manualSortingEnabled")) {
                $this->sortParameters['manual'] = [
                    'url' => $filteredUrl,
                    'active' => false,
                    'reversable' => false,
                ];
            }

            if ($this->isSettingEnabled("priceSortingEnabled")) {
                $this->sortParameters['price'] = [
                    'url' => $filteredUrl . 'sort:price/',
                    'active' => false,
                    'reversable' => true,
                ];
            }
            if ($this->isSettingEnabled("nameSortingEnabled")) {
                $this->sortParameters['title'] = [
                    'url' => $filteredUrl . 'sort:title/',
                    'active' => false,
                    'reversable' => true,
                ];
            }
            if ($this->isSettingEnabled("dateSortingEnabled")) {
                $this->sortParameters['date'] = [
                    'url' => $filteredUrl . 'sort:date/',
                    'active' => false,
                    'reversable' => true,
                ];
            }
            $activeSortArgument = $arguments['sort'];
            $activeOrderArgument = $arguments['order'];
            if (isset($this->sortParameters[$arguments['sort']])) {
                $this->sortParameters[$activeSortArgument]['active'] = true;
                if ($activeSortArgument !== 'manual' && $activeOrderArgument !== 'desc') {
                    $this->sortParameters[$activeSortArgument]['url'] = $filteredUrl . 'sort:' . $arguments['sort'] . ';' . 'desc/';
                }
            }
        }
        return $this->sortParameters;
    }

    protected function isFieldSortable($field)
    {
        switch ($field) {
            case 'manual':
                return $this->isSettingEnabled('manualSortingEnabled');
            case 'price':
                return $this->isSettingEnabled('priceSortingEnabled');
            case 'title':
                return $this->isSettingEnabled('nameSortingEnabled');
            case 'date':
                return $this->isSettingEnabled('dateSortingEnabled');
        }
        return false;
    }

    protected function getParentRestrictionId()
    {
        return $this->id;
    }

    /**
     * @deprecated
     */
    public function getSelectedUsedParameters()
    {
        if (!is_null($this->selectedUsedParameters)) {
            return $this->selectedUsedParameters;
        }

        $this->selectedUsedParameters = [];

        if ($usedParametersIds = $this->getUsedParametersIds()) {
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            $connectedParameterIds = $linksManager->getConnectedIdList($this->id, "categoryParameter", "parent");

            foreach ($connectedParameterIds as &$parameterId) {
                if ($parameterElement = $structureManager->getElementById($parameterId)) {
                    if ($parameterElement->structureType == "productParameter") {
                        if (in_array($parameterElement->id, $usedParametersIds)) {
                            $this->selectedUsedParameters[] = $parameterElement;
                        }
                    }
                }
            }
        }

        return $this->selectedUsedParameters;
    }

    /**
     * @deprecated
     */
    public function getUsedParametersIds()
    {
        if (!is_null($this->usedParametersIds)) {
            return $this->usedParametersIds;
        }
        $this->usedParametersIds = [];
        $products = $this->getProductsList();
        foreach ($products as &$product) {
            foreach ($product->getParametersGroupsInfo() as $parameterGroupInfo) {
                foreach ($parameterGroupInfo['parametersList'] as $parameterInfo) {
                    if (!in_array($parameterInfo['id'], $this->usedParametersIds)) {
                        $this->usedParametersIds[] = $parameterInfo['id'];
                    }
                }
            }
        }
        return $this->usedParametersIds;
    }

    protected function getSelectionIdsForFiltering()
    {
        if ($this->selectionsIdsForFiltering === null) {
            $structureManager = $this->getService('structureManager');
            $currentLanguageId = $this->getService('languagesManager')->getCurrentLanguageId();
            $productSearchElements = $structureManager->getElementsByType('productSearch', $currentLanguageId);
            $gotPageIndependentProductSearches = false;
            foreach ($productSearchElements as &$element) {
                if (!$element->pageDependent) {
                    $gotPageIndependentProductSearches = true;
                    break;
                }
            }
            if (!$gotPageIndependentProductSearches) {
                $this->selectionsIdsForFiltering = $this->getSelectionsIdsConnectedForFiltering();
            } else {
                $this->selectionsIdsForFiltering = parent::getSelectionIdsForFiltering();
            }
        }
        return $this->selectionsIdsForFiltering;
    }

    public function getSelectionsIdsConnectedForFiltering()
    {
        $linksManager = $this->getService('linksManager');
        return $linksManager->getConnectedIdList($this->id, 'productSelectionFilterableCategory', 'parent');
    }
}
