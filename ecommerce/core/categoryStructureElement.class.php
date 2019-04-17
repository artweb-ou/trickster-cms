<?php

abstract class categoryStructureElement extends ProductsListStructureElement
{
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

    protected function isFilterable()
    {
        return $this->productsLayout !== 'hide' && parent::isFilterable();
    }

    protected function isFilterableByType($filterType)
    {
        if ($this->role == 'content' || $this->requested) {
            switch ($filterType) {
                case 'category':
                    $result = true;
//                    $result = $this->isSettingEnabled('categoryFilterEnabled');
                    break;
                case 'brand':
                    $result = $this->isSettingEnabled('brandFilterEnabled');
                    break;
                case 'discount':
                    $result = $this->isSettingEnabled('discountFilterEnabled');
                    break;
                case 'parameter':
                    $result = $this->isSettingEnabled('parameterFilterEnabled');
                    break;
                case 'price':
                    $result = true;
//                    $result = $this->isSettingEnabled('priceFilterEnabled');
                    break;
                case 'availability':
                    $result = $this->isSettingEnabled('availabilityFilterEnabled');
                    break;
                default:
                    $result = true;
            }
        }
        return $result;
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

    protected function getProductsListParentRestrictionId()
    {
        return $this->id;
    }

    protected function getSelectionIdsForFiltering()
    {
        if ($this->selectionsIdsForFiltering === null) {
            $this->selectionsIdsForFiltering = $this->getSelectionsIdsConnectedForFiltering();
        }
        return $this->selectionsIdsForFiltering;
    }

    public function getSelectionsIdsConnectedForFiltering()
    {
        $linksManager = $this->getService('linksManager');
        return $linksManager->getConnectedIdList($this->id, 'productSelectionFilterableCategory', 'parent');
    }

    /**
     * @param $settingName
     * @return bool
     */
    public function isSettingEnabled($settingName)
    {
        $enabled = false;
        switch ($this->$settingName) {
            case 0:
                $enabled = false;
                break;
            case 1:
                $enabled = true;
                break;
            case 2:
                $enabled = false;
        }
        return $enabled;
    }

    abstract public function getCategoriesList();

    public function isAmountSelectionEnabled()
    {
        return $this->isSettingEnabled('amountOnPageEnabled');
    }

    public function getProductsListCategories()
    {
        return $this->getCategoriesList();
    }
}
