<?php

class ParametersManager extends errorLogger
{
    protected $productParameters = [];
    protected $productParametersValues = [];
    protected $productPrimaryParameters = [];
    protected $productBasketSelections = [];
    protected $allPrimaryParametersInfo;
    /**
     * @var Illuminate\Database\Capsule\Manager
     */
    protected $db;
    /**
     * @var LanguagesManager
     */
    protected $languagesManager;

    /**
     * @param LanguagesManager $languagesManager
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
        if (!isset($this->productParametersValues[$productId])) {
            $this->loadParameterValuesOfProduct($productId);
        }
        return isset($this->productParametersValues[$productId][$parameterId])
            ? $this->productParametersValues[$productId][$parameterId]
            : '';
    }

    public function getProductParametersValues($productId)
    {
        if (!isset($this->productParametersValues[$productId])) {
            $this->loadParameterValuesOfProduct($productId);
        }
        return $this->productParametersValues[$productId];
    }

    protected function loadParameterValuesOfProducts($productsIds)
    {
        $currentLanguageId = $this->languagesManager->getCurrentLanguageId();
        foreach ($productsIds as $productId) {
            $this->productParametersValues[$productId] = [];
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
                $productParameters = &$this->productParametersValues[$record['productId']];
                if (!isset($productParameters[$record['parameterId']])) {
                    $productParameters[$record['parameterId']] = [];
                }
                $productParameters[$record['parameterId']][] = $record['value'];
            }
        }
    }

    public function loadParameterValuesOfProduct($productId)
    {
        if (!isset($this->productParametersValues[$productId])) {
            $this->productParametersValues[$productId] = [];
            $productParameters = &$this->productParametersValues[$productId];

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

    protected function loadParametersForProducts($productIdList)
    {
        //let's only preload parameters we need.
        foreach ($productIdList as $key => $id) {
            if (isset($this->productParameters[$id])) {
                unset($productIdList[$key]);
            } else {
                //this should be empty array, not null, otherwize isset() gives false.
                $this->productParameters[$id] = [];
                $this->productBasketSelections[$id] = [];
            }
        }

        //check if something has to be preloaded at all.
        if ($productIdList) {
            //load all primary parameters info at once.
            if ($primaryParametersInfo = $this->getProductsParametersInfo($productIdList)) {
                if ($result = $this->processParameters($primaryParametersInfo, $productIdList)) {
                    foreach ($result['parameters'] as $productId => $info) {
                        $this->productParameters[$productId] = $info;
                    }
                    foreach ($result['basketSelections'] as $productId => $info) {
                        $this->productBasketSelections[$productId] = $info;
                    }
                }
            }
        }
    }

    protected function loadPrimaryParametersForProducts($productIdList)
    {
        //let's only preload parameters we need.
        foreach ($productIdList as $key => $id) {
            if (isset($this->productPrimaryParameters[$id])) {
                unset($productIdList[$key]);
            } else {
                $this->productPrimaryParameters[$id] = [];
            }
        }

        //check if something has to be preloaded at all.
        if ($productIdList) {
            //load all primary parameters info at once.
            if ($primaryParametersInfo = $this->getAllPrimaryParametersInfo()) {
                if ($result = $this->processParameters($primaryParametersInfo, $productIdList)) {
                    foreach ($result['parameters'] as $productId => $info) {
                        $this->productPrimaryParameters[$productId] = $info;
                    }
                }
            }
        }
    }

    protected function processParameters($parametersInfo, $productIdList)
    {
        $result = [
            'parameters' => [],
            'basketSelections' => [],
        ];

        $currentLanguageId = $this->languagesManager->getCurrentLanguageId();

        $parametersInfoIndex = $parametersInfo['parametersInfoIndex'];
        $selectionsInfoIndex = $parametersInfo['selectionsInfoIndex'];
        $parametersPositionsIndex = $parametersInfo['parametersPositionsIndex'];

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
        if ($parametersInfo['parameterIds']) {
            //load all values for all requested products and all primary parameters
            $query = $this->db->table('module_product_parameter_value')
                ->select([
                    'module_product_parameter_value.productId',
                    'module_product_parameter_value.parameterId',
                    'module_product_parameter_value.value',
                ])
                ->whereIn('module_product_parameter_value.productId', $productIdList)
                ->whereIn('module_product_parameter_value.languageId', [$currentLanguageId, '0'])
                ->whereIn('module_product_parameter_value.parameterId', $parametersInfo['parameterIds'])
                //sorted by position of parameters
                ->leftJoin('structure_links', 'module_product_parameter_value.parameterId', '=', 'structure_links.childStructureId')
                //then sorted by position of values inside parameters.
                ->leftJoin('structure_links as links2', function ($query) {
                    $query->on('module_product_parameter_value.value', '=', 'links2.childStructureId')
                        ->where('links2.type', '=', 'structure');
                })
                ->groupBy(
                    'module_product_parameter_value.productId',
                    'module_product_parameter_value.parameterId',
                    'module_product_parameter_value.value'
                )
                ->orderBy('structure_links.position', 'asc')
                ->orderBy('links2.position', 'asc');
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
                foreach ($valuesList as $record) {
                    $position = null;
                    $parameterId = $record['parameterId'];

                    //build up positions info for sorting.
                    //not all products can have parent elements with sorting, some are not attached to categories
                    if (isset($productParentsInfo[$record['productId']])) {
                        foreach ($productParentsInfo[$record['productId']] as $parentId) {
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
                        $productParameters = &$result['parameters'][$record['productId']];

                        //if it's parameter selection, then use options list, otherwise use simple text value.
                        if (isset($selectionsInfoIndex[$parameterId])) {
                            if ($selectionsInfoIndex[$parameterId]['option']) {
                                $productBasketSelections = &$result['basketSelections'][$record['productId']];
                                if (!isset($productBasketSelections[$parameterId])) {
                                    $productBasketSelections[$parameterId] = $selectionsInfoIndex[$parameterId];
                                    $productBasketSelections[$parameterId]['structureType'] = 'productSelection';
                                }
                                $productBasketSelections[$parameterId]['productOptions'][] = $optionsInfoIndex[$record['value']];
                            }
                            if (!isset($productParameters[$parameterId])) {
                                $productParameters[$parameterId] = $selectionsInfoIndex[$parameterId];
                                $productParameters[$parameterId]['structureType'] = 'productSelection';
                            }
                            $productParameters[$parameterId]['productOptions'][] = $optionsInfoIndex[$record['value']];

                        } elseif (isset($parametersInfoIndex[$parameterId])) {
                            if (!isset($productParameters[$parameterId])) {
                                $productParameters[$parameterId] = $parametersInfoIndex[$parameterId];
                                $productParameters[$parameterId]['structureType'] = 'productParameter';
                            }
                            $productParameters[$parameterId]['value'] = $record['value'];
                        }
                    }
                }
                //sort every product's parameters positions according to product parents settings
                foreach ($parameterPositions as $productId => $sortList) {
                    array_multisort($sortList, SORT_ASC, $result['parameters'][$productId]);
                }
                foreach ($selectionPositions as $productId => $sortList) {
                    array_multisort($sortList, SORT_ASC, $result['basketSelections'][$productId]);
                }
            }
        }
        return $result;
    }

    protected function getAllPrimaryParametersInfo()
    {
        if ($this->allPrimaryParametersInfo === null) {
            $this->allPrimaryParametersInfo = $this->getParametersInfo(null, true);
        }
        return $this->allPrimaryParametersInfo;
    }

    protected function getProductsParametersInfo($productsIdList)
    {
        $query = $this->db->table('module_product_parameter_value')
            ->whereIn('productId', $productsIdList)
            ->select('parameterId')->distinct();
        if ($records = $query->get()) {
            return $this->getParametersInfo(array_column($records, 'parameterId'));
        }
        return [];
    }

    protected function getParametersInfo($idList = null, $primary = false)
    {
        $currentLanguageId = $this->languagesManager->getCurrentLanguageId();

        $parametersInfo = [
            'parametersInfoIndex' => [],
            'selectionsInfoIndex' => [],
            'parameterIds' => [],
            'parametersPositionsIndex' => [],
        ];
        $query = $this->db->table('module_product_parameter')
            ->select('id', 'title', 'originalName', 'image')
            ->where('languageId', "=", $currentLanguageId);

        if ($primary) {
            $query->where('primary', "=", '1');
        }
        if ($idList !== null) {
            $query->whereIn('id', $idList);
        }

        if ($records = $query->get()) {
            $parametersInfo['parameterIds'] = array_column($records, 'id');
            foreach ($records as $parameterInfo) {
                $parametersInfo['parametersInfoIndex'][$parameterInfo['id']] = $parameterInfo;
            }
        }

        $query = $this->db->table('module_product_selection')
            ->select('id', 'title', 'type', 'option', 'controlType', 'influential', 'hint', 'originalName', 'image')
            ->where('languageId', "=", $currentLanguageId);
        if ($primary) {
            $query->where('primary', "=", '1');
        }
        if ($idList !== null) {
            $query->whereIn('id', $idList);
        }
        if ($records = $query->get()) {
            $parametersInfo['parameterIds'] = array_merge($parametersInfo['parameterIds'], array_unique(array_column($records, 'id')));
            foreach ($records as $selectionInfo) {
                $parametersInfo['selectionsInfoIndex'][$selectionInfo['id']] = $selectionInfo;
                if ($selectionInfo['option']) {
                    $parametersInfo['basketSelectionsInfoIndex'][] = $selectionInfo;
                }
            }
        }
        if ($parametersInfo['parameterIds']) {
            $parametersInfo['parametersPositionsIndex'] = [];
            if ($linksInfo = $this->db->table('structure_links')
                ->select(['childStructureId', 'parentStructureId', 'position'])
                ->whereIn('childStructureId', $parametersInfo['parameterIds'])
                ->where('type', '=', 'categoryParameter')
                ->get()
            ) {
                foreach ($linksInfo as $linksInfoItem) {
                    $parametersInfo['parametersPositionsIndex'][$linksInfoItem['parentStructureId']][$linksInfoItem['childStructureId']] = $linksInfoItem['position'];
                }
            }
        }
        return $parametersInfo;
    }

    public function getProductPrimaryParametersInfo($productId)
    {
        if (!isset($this->productPrimaryParameters[$productId])) {
            $this->loadPrimaryParametersForProducts([$productId]);
        }
        return $this->productPrimaryParameters[$productId];
    }

    public function getProductBasketSelectionsInfo($productId)
    {
        if (!isset($this->productBasketSelections[$productId])) {
            $this->loadParametersForProducts([$productId]);
            foreach ($this->productBasketSelections[$productId] as $key => $productSelection) {
                if (!$productSelection['productOptions']) {
                    unset($this->productBasketSelections[$productId][$key]);
                }
            }
        }
        return $this->productBasketSelections[$productId];
    }
}