<?php

class ParametersManager extends errorLogger
{
    protected $parameters = [];
    protected $primaryParameters = [];
    protected $basketSelections = [];
    protected $allPrimaryParametersInfo;
    /**
     * @var Illuminate\Database\Capsule\Manager
     */
    protected $db;
    /**
     * @var languagesManager
     */
    protected $languagesManager;

    /**
     * @param languagesManager $languagesManager
     */
    public function setLanguagesManager($languagesManager)
    {
        $this->languagesManager = $languagesManager;
    }

    /**
     * @param Illuminate\Database\Capsule\Manager() $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    public function getProductParameterValues($productId, $parameterId)
    {
        if (!isset($this->parameters[$productId])) {
            $this->loadParameterValuesOfProduct($productId);
        }
        return isset($this->parameters[$productId][$parameterId])
            ? $this->parameters[$productId][$parameterId]
            : '';
    }

    public function getProductParametersValues($productId)
    {
        if (!isset($this->parameters[$productId])) {
            $this->loadParameterValuesOfProduct($productId);
        }
        return $this->parameters[$productId];
    }

    public function loadParameterValuesOfProducts($productsIds)
    {
        $currentLanguageId = $this->languagesManager->getCurrentLanguageId();
        foreach ($productsIds as $productId) {
            $this->parameters[$productId] = [];
        }
        $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
        $searchFields = [
            ['productId', 'IN', $productsIds],
            ['languageId', 'IN', [$currentLanguageId, '0']],
        ];
        if ($valuesList = $valuesCollection->conditionalLoad([
            'productId',
            'parameterId',
            'value',
        ], $searchFields)
        ) {
            foreach ($valuesList as &$record) {
                $productParameters = &$this->parameters[$record['productId']];
                if (!isset($productParameters[$record['parameterId']])) {
                    $productParameters[$record['parameterId']] = [];
                }
                $productParameters[$record['parameterId']][] = $record['value'];
            }
        }
    }

    public function loadParameterValuesOfProduct($productId)
    {
        if (!isset($this->parameters[$productId])) {
            $this->parameters[$productId] = [];
            $productParameters = &$this->parameters[$productId];

            $currentLanguageId = $this->languagesManager->getCurrentLanguageId();

            $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
            $searchFields = [
                ['productId', '=', $productId],
                ['languageId', 'in', [$currentLanguageId, '0']],
            ];
            if ($valuesList = $valuesCollection->conditionalLoad(['parameterId', 'value'], $searchFields)) {
                foreach ($valuesList as &$record) {
                    if (!isset($productParameters[$record['parameterId']])) {
                        $productParameters[$record['parameterId']] = [];
                    }
                    $productParameters[$record['parameterId']][] = $record['value'];
                }
            }
        }
    }

    public function preloadPrimaryParametersForProducts($productIdList)
    {
        //let's only preload primary parameters we need.
        foreach ($productIdList as $key => $id) {
            if (isset($this->primaryParameters[$id])) {
                unset($productIdList[$key]);
            } else {
                //this should be empty array, not null, otherwize isset() gives false.
                $this->primaryParameters[$id] = [];
                $this->basketSelections[$id] = [];
            }
        }

        //check if something has to be preloaded at all.
        if ($productIdList) {
            $currentLanguageId = $this->languagesManager->getCurrentLanguageId();

            //load all primary parameters info at once.
            if ($primaryParametersInfo = $this->getAllPrimaryParametersInfo()) {
                $parametersInfoIndex = $primaryParametersInfo['parametersInfoIndex'];
                $selectionsInfoIndex = $primaryParametersInfo['selectionsInfoIndex'];
                $primaryParameterIds = $primaryParametersInfo['primaryParameterIds'];
                $parametersPositionsIndex = $primaryParametersInfo['parametersPositionsIndex'];

                //take all product parents IDs from DB, sort them into quicker index
                //takes about 1/4 from overall procedure speed
                $productParentsInfo = [];
                if ($linksInfo = $this->db->table('structure_links')
                    ->select(['childStructureId', 'parentStructureId'])
                    ->whereIn('childStructureId', $productIdList)
                    ->whereIn('type', ['catalogue', 'productCatalogueProduct'])
                    ->get()
                ) {
                    foreach ($linksInfo as $linksInfoItem) {
                        $productParentsInfo[$linksInfoItem['childStructureId']][] = $linksInfoItem['parentStructureId'];
                    }
                }

                $optionsInfoIndex = [];
                if ($primaryParameterIds) {
                    //load all values for all requested products and all primary parameters.
                    $query = $this->db->table('module_product_parameter_value')
                        ->select([
                            'module_product_parameter_value.productId',
                            'module_product_parameter_value.parameterId',
                            'module_product_parameter_value.value',
                        ])
                        ->whereIn('module_product_parameter_value.productId', $productIdList)
                        ->whereIn('module_product_parameter_value.languageId', [$currentLanguageId, '0'])
                        ->whereIn('module_product_parameter_value.parameterId', $primaryParameterIds)
                        ->leftJoin('structure_links', 'module_product_parameter_value.parameterId', '=', 'structure_links.childStructureId')
                        ->groupBy(
                            'module_product_parameter_value.productId',
                            'module_product_parameter_value.parameterId',
                            'module_product_parameter_value.value'
                        )
                        ->orderBy('structure_links.position', 'asc');
                    if ($valuesList = $query->get()
                    ) {
                        //load all options (product selection values) for all primary product selections
                        if ($optionsIdList = array_unique(array_column($valuesList, 'value'))) {
                            if ($optionsInfoList = $this->db->table('module_product_selection_value')
                                ->select(['id', 'title', 'image', 'originalName', 'value'])
                                ->whereIn('id', $optionsIdList)
                                ->where('languageId', '=', $currentLanguageId)
                                ->get()
                            ) {
                                foreach ($optionsInfoList as $optionsInfoItem) {
                                    $optionsInfoIndex[$optionsInfoItem['id']] = $optionsInfoItem;
                                }
                            }
                        }
                        $parameterPositions = [];
                        $selectionPositions = [];
                        //apply values to product primary parameters
                        foreach ($valuesList as &$record) {
                            $position = null;
                            $parameterId = $record['parameterId'];

                            //build up positions info for sorting.
                            //not all products can have parent elements with sorting, some are not attached to categories
                            if (isset($productParentsInfo[$record['productId']])) {
                                foreach ($productParentsInfo[$record['productId']] as &$parentId) {
                                    //for some categories product parameters positions can be missing if category was added later
                                    if (isset($parametersPositionsIndex[$parentId][$parameterId]) && ($position = $parametersPositionsIndex[$parentId][$parameterId]) !== null) {
                                        //we don't need basket selection parameters in sorting, they always go separately
                                        if (!isset($selectionsInfoIndex[$parameterId]) || !$selectionsInfoIndex[$parameterId]['option']) {
                                            $parameterPositions[$record['productId']][$parameterId] = $position;
                                        } else {
                                            $selectionPositions[$record['productId']][$parameterId] = $position;
                                        }
                                        break;
                                    }
                                }
                            }
                            //if we have no position, then it means that parameter is not connected to category or
                            //product catalogue, which means we shouldn't display it at all.
                            if ($position !== null) {
                                //make reference for readability, doesn't really affect speed
                                $productPrimaryParameters = &$this->primaryParameters[$record['productId']];

                                //if it's parameter selection, then use options list, otherwise use simple text value.
                                if (isset($selectionsInfoIndex[$parameterId])) {
                                    if ($selectionsInfoIndex[$parameterId]['option']) {
                                        $productBasketSelections = &$this->basketSelections[$record['productId']];
                                        if (!isset($productBasketSelections[$parameterId])) {
                                            $productBasketSelections[$parameterId] = $selectionsInfoIndex[$parameterId];
                                            $productBasketSelections[$parameterId]['structureType'] = 'productSelection';
                                        }
                                        $productBasketSelections[$parameterId]['productOptions'][] = $optionsInfoIndex[$record['value']];
                                    }
                                    if (!isset($productPrimaryParameters[$parameterId])) {
                                        $productPrimaryParameters[$parameterId] = $selectionsInfoIndex[$parameterId];
                                        $productPrimaryParameters[$parameterId]['structureType'] = 'productSelection';
                                    }
                                    $productPrimaryParameters[$parameterId]['productOptions'][] = $optionsInfoIndex[$record['value']];

                                } elseif (isset($parametersInfoIndex[$parameterId])) {
                                    if (!isset($productPrimaryParameters[$parameterId])) {
                                        $productPrimaryParameters[$parameterId] = $parametersInfoIndex[$parameterId];
                                        $productPrimaryParameters[$parameterId]['structureType'] = 'productParameter';
                                    }
                                    $productPrimaryParameters[$parameterId]['value'] = $record['value'];
                                }
                            }
                        }

                        //sort every product's parameters positions according to product parents settings
                        foreach ($parameterPositions as $productId => $sortList) {
                            array_multisort($sortList, SORT_ASC, $this->primaryParameters[$productId]);
                        }
                        foreach ($selectionPositions as $productId => $sortList) {
                            array_multisort($sortList, SORT_ASC, $this->basketSelections[$productId]);
                        }
                    }
                }
            }
        }
    }

    protected function getAllPrimaryParametersInfo()
    {
        if ($this->allPrimaryParametersInfo === null) {
            $currentLanguageId = $this->languagesManager->getCurrentLanguageId();

            $this->allPrimaryParametersInfo = [
                'parametersInfoIndex' => [],
                'selectionsInfoIndex' => [],
                'primaryParameterIds' => [],
                'parametersPositionsIndex' => [],
            ];

            if ($records = $this->db->table('module_product_parameter')
                ->select('id', 'title', 'originalName', 'image')
                ->where('primary', "=", '1')
                ->where('languageId', "=", $currentLanguageId)
                ->get()
            ) {
                $this->allPrimaryParametersInfo['primaryParameterIds'] = array_column($records, 'id');
                foreach ($records as $parameterInfo) {
                    $this->allPrimaryParametersInfo['parametersInfoIndex'][$parameterInfo['id']] = $parameterInfo;
                }
            }
            if ($records = $this->db->table('module_product_selection')
                ->select('id', 'title', 'type', 'option', 'controlType', 'influential', 'hint')
                ->orWhere(function ($query) use ($currentLanguageId) {
                    $query->where('primary', '=', 1)
                        ->where('languageId', "=", $currentLanguageId);
                }
                )
                ->orWhere(function ($query) use ($currentLanguageId) {
                    $query->where('option', '=', 1)
                        ->where('languageId', "=", $currentLanguageId);
                }
                )
                ->get()
            ) {
                $this->allPrimaryParametersInfo['primaryParameterIds'] = array_merge($this->allPrimaryParametersInfo['primaryParameterIds'], array_unique(array_column($records, 'id')));
                foreach ($records as $selectionInfo) {
                    $this->allPrimaryParametersInfo['selectionsInfoIndex'][$selectionInfo['id']] = $selectionInfo;
                    if ($selectionInfo['option']) {
                        $this->allPrimaryParametersInfo['basketSelectionsInfoIndex'][] = $selectionInfo;
                    }
                }
            }
            if ($this->allPrimaryParametersInfo['primaryParameterIds']) {
                $this->allPrimaryParametersInfo['parametersPositionsIndex'] = [];
                if ($linksInfo = $this->db->table('structure_links')
                    ->select(['childStructureId', 'parentStructureId', 'position'])
                    ->whereIn('childStructureId', $this->allPrimaryParametersInfo['primaryParameterIds'])
                    ->where('type', '=', 'categoryParameter')
                    ->get()
                ) {
                    foreach ($linksInfo as $linksInfoItem) {
                        $this->allPrimaryParametersInfo['parametersPositionsIndex'][$linksInfoItem['parentStructureId']][$linksInfoItem['childStructureId']] = $linksInfoItem['position'];
                    }
                }
            }
        }
        return $this->allPrimaryParametersInfo;
    }

    public function getProductPrimaryParametersInfo($productId)
    {
        if (!isset($this->primaryParameters[$productId])) {
            $this->preloadPrimaryParametersForProducts([$productId]);
        }
        return $this->primaryParameters[$productId];
    }

    public function getProductBasketSelectionsInfo($productId)
    {
        if (!isset($this->basketSelections[$productId])) {
            $this->preloadPrimaryParametersForProducts([$productId]);
        }
        return $this->basketSelections[$productId];
    }
}