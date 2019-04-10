<?php

abstract class productsListStructureElement extends menuStructureElement
{
    use ProductFilterFactoryTrait;
    use MasterCategoriesQueringTrait;
    protected $brandFiltered = true;
    protected $discountFiltered = true;
    protected $parameterFiltered = true;
    protected $productsList;
    protected $productsPager;
    protected $brandsList;
    protected $discountsList;
    protected $connectedProductsIds;
    protected $selectedBrandsIdList;
    protected $selectedFilterValues;
    protected $selectedSortParameter;
    protected $selectedDiscountId;
    protected $productSelectionFilters;
    protected $sortParameters;
    protected $limits;
    protected $searchArguments;
    protected $priceRangeOptions;
    /**@var productFilter */
    protected $baseFilter;
    protected $filtersIndex = [];
    protected $productSearchFilterTypes;
    protected $selectionsIdsForFiltering;
    protected $sortingOptions;
    protected $usedParametersInfo;
    protected $usedParametersInfoIndex;

    public function getFiltersByType($type)
    {
        return isset($this->filtersIndex[$type]) ? $this->filtersIndex[$type] : [];
    }

    public function getFiltersIndex()
    {
        return $this->filtersIndex;
    }

    protected function registerFilter(productFilter $filter)
    {
        $type = $filter->getType();
        if (!isset($this->filtersIndex[$type])) {
            $this->filtersIndex[$type] = [];
        }
        $this->filtersIndex[$type][] = $filter;
    }

    public function prepareFilters($arguments)
    {
        if ($this->baseFilter === null) {
            if ($this->isFilterNeeded('category')) {
                if ($categoriesIds = $this->getTopLevelCategoriesIds()) {
                    $this->addFilter($this->createProductFilter('category', $arguments['category'], $categoriesIds));
                }
            }
            if ($this->isFilterNeeded('brand')) {
                $filter = $this->createProductFilter('brand', $arguments['brand']);
                $this->addFilter($filter);
            }
            if ($this->isFilterNeeded('discount')) {
                $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
                $discountsList = $shoppingBasketDiscounts->getApplicableDiscountsList();
                if ($discountsList) {
                    $filter = $this->createProductFilter('discount', $arguments['discount']);
                    $this->addFilter($filter);
                }
            }
            if ($this->isFilterNeeded('availability')) {
                $filter = $this->createProductFilter('availability', $arguments['availability']);
                $this->addFilter($filter);
            }
            if ($this->isFilterNeeded('parameter')) {
                $this->prepareParametersFilters($arguments);
            }
            if ($this->isFilterNeeded('price')) {
                $filter = $this->createProductFilter('price', $arguments['price']);
                $structureManager = $this->getService('structureManager');
                $languageId = $this->getService('languagesManager')->getCurrentLanguageId();
                $elements = $structureManager->getElementsByType('productSearch', $languageId);
                if ($elements) {
                    $productSearchElement = $elements[0];
                    $filter->setRangeInterval($productSearchElement->priceInterval);
                }
                $this->addFilter($filter);
            }
        }
    }

    protected function isFilterNeeded($filterName)
    {
        $result = $this->isFilterUsedInProductSearch($filterName);
        if ($this->role == 'content' || (!$result && $this->requested)) {
            switch ($filterName) {
                case 'category':
                    $result = $this->isFilterableByCategory();
                    break;
                case 'brand':
                    $result = $this->isFilterableByBrand();
                    break;
                case 'discount':
                    $result = $this->isFilterableByDiscount();
                    break;
                case 'parameter':
                    $result = $this->isFilterableByParameter();
                    break;
                case 'price':
                    $result = $this->isFilterableByPrice();
                    break;
                case 'availability':
                    $result = $this->isFilterableByAvailability();
                    break;
                default:
                    $result = true;
            }
        }
        return $result;
    }

    protected function isFilterUsedInProductSearch($filterName)
    {
        $types = $this->getCurrentProductSearchFilterTypes();
        return isset($types[$filterName]);
    }

    protected function getCurrentProductSearchFilterTypes()
    {
        if ($this->productSearchFilterTypes === null) {
            $this->productSearchFilterTypes = [];
            $structureManager = $this->getService('structureManager');
            $currentLanguageElementId = $this->getService('languagesManager')->getCurrentLanguageId();
            $currentLanguageElement = $structureManager->getElementById($currentLanguageElementId);

            $currentMainMenu = false;
            $childElements = $currentLanguageElement->getChildrenList();
            if ($childElements) {
                foreach ($childElements as &$element) {
                    if ($element->requested) {
                        $currentMainMenu = $element;
                    }
                }
                if (!$currentMainMenu) {
                    $currentMainMenu = $childElements[0];
                }

                $widgetElements = $currentLanguageElement->getLeftColumnElementsList();
                $widgetElements = array_merge($widgetElements, $currentLanguageElement->getRightColumnElementsList());
                $headerElements = $currentLanguageElement->getElementsFromHeader('productSearch') ?: [];
                $footerElements = $currentLanguageElement->getElementsFromFooter('productSearch') ?: [];
                $widgetElements = array_merge($widgetElements, $headerElements, $footerElements);
                foreach ($widgetElements as $widgetElement) {
                    if ($widgetElement->structureType != 'productSearch') {
                        continue;
                    }
                    if ($widgetElement->filterCategory) {
                        $this->productSearchFilterTypes['category'] = true;
                    }
                    if ($widgetElement->filterBrand) {
                        $this->productSearchFilterTypes['brand'] = true;
                    }
                    if ($widgetElement->filterDiscount) {
                        $this->productSearchFilterTypes['discount'] = true;
                    }
                    if ($widgetElement->availabilityFilterEnabled) {
                        $this->productSearchFilterTypes['availability'] = true;
                    }
                    if ($widgetElement->filterPrice) {
                        $this->productSearchFilterTypes['price'] = true;
                    }
                    $currentElement = $widgetElement->getCurrentElement();
                    if ($currentElement instanceof productsListStructureElement || $currentElement instanceof productElement) {
                        $this->productSearchFilterTypes['parameter'] = true;
                    }
                }
            }
        }
        return $this->productSearchFilterTypes;
    }

    protected function prepareParametersFilters($arguments)
    {
        $selections = $this->getParametersForFiltering();
        foreach ($selections as &$selectionInfo) {
            $filter = $this->createProductFilter('parameter', array_intersect($arguments['parameter'], $selectionInfo['options']), $selectionInfo['options']);
            $filter->setSelectionId($selectionInfo['selection']->id);
            $this->addFilter($filter);
        }
    }

    public function addFilter(productFilter $filter)
    {
        if ($this->baseFilter !== null) {
            $this->baseFilter->addFilter($filter);
        } else {
            $this->baseFilter = $filter;
        }
        $this->registerFilter($filter);
    }

    public abstract function getConnectedProductsIds();

    public function getDefaultOrder()
    {
        return $this->defaultOrder ? $this->defaultOrder : 'manual';
    }

    final public function getActiveProductsIds()
    {
        static $result;

        if ($result === null) {
            $cacheKey = 'productsList_getActiveProductsIds_' . $this->id;
            if (($result = $this->getService('Cache')->get($cacheKey)) === false) {
                $result = [];
                $collection = persistableCollection::getInstance("module_product");
                $orConditions = [
                    [
                        [
                            "availability",
                            "!=",
                            "unavailable",
                        ],
                        [
                            'availability',
                            '!=',
                            'quantity_dependent',
                        ],
                        [
                            'inactive',
                            '!=',
                            '1',
                        ],
                    ],
                    [
                        [
                            "availability",
                            "=",
                            "quantity_dependent",
                        ],
                        [
                            "quantity",
                            "!=",
                            "0",
                        ],
                        [
                            'inactive',
                            '!=',
                            '1',
                        ],
                    ],
                ];
                if ($records = $collection->conditionalOrLoad('distinct(id)', $orConditions, [], [], [], true)) {
                    $result = array_column($records, 'id');
                }
                $this->getService('Cache')->set($cacheKey, $result, 120, $this->id);
            }
        }
        return $result;
    }

    public function getProductsListBaseIds()
    {
        $result = [];
        if ($activeProductsIds = $this->getActiveProductsIds()) {
            $conditions[] = [
                "parentStructureId",
                "IN",
                $this->getProductsListParentElementsIds(),
            ];
            $conditions[] = [
                "childStructureId",
                "in",
                $activeProductsIds,
            ];
            if ($records = persistableCollection::getInstance("structure_links")
                ->conditionalLoad('distinct(childStructureId)', $conditions, [], [], [], true)
            ) {
                $result = array_column($records, 'childStructureId');
            }
        }
        return $result;
    }

    public function doFiltration($arguments)
    {
        $this->filtersIndex = null;
        $this->baseFilter = null;
        $productsIds = $this->getProductsListBaseIds();
        if ($productsIds) {
            $this->prepareFilters($arguments);
            if ($this->baseFilter !== null) {
                $this->baseFilter->apply($productsIds);
            }
        }
        return $productsIds;
    }

    /**
     * @return productElement[]
     */
    public function getProductsList()
    {
        if ($this->productsList !== null) {
            return $this->productsList;
        }
        $this->productsList = [];
        $arguments = $this->parseSearchArguments();
        $productsIds = $this->doFiltration($arguments);
        if ($productsIds) {
            $elementsOnPage = $arguments['limit'];
            $pager = new pager($this->generatePagerUrl($arguments), count($productsIds), $elementsOnPage, (int)controller::getInstance()
                ->getParameter('page'), "page");
            $this->productsPager = $pager;

            $database = $this->getService('db');
            $query = $database->table('module_product')
                ->select(('module_product.id'))
                ->whereIn('module_product.id', $productsIds);

            $manualOrdering = $arguments['sort'] && $arguments['sort'] == 'manual';
            if (!$manualOrdering) {
                if ($arguments['sort'] == 'date') {
                    $query->join('structure_elements', 'module_product.id', '=', 'structure_elements.id', 'left');
                    $query->orderBy('structure_elements.dateCreated', $arguments['order']);
                } elseif ($arguments['sort'] == 'brand') {
                    $query->join('module_brand', 'module_product.brandId', '=', 'module_brand.id', 'left');
                    $query->orderBy('module_brand.title', $arguments['order']);
                } else {
                    $query->orderBy($arguments['sort'], $arguments['order']);
                }
            } else {
                $this->applyManualSorting($query, $productsIds);
            }
            $query->skip($pager->startElement)->take($elementsOnPage)->groupBy('module_product.id');
            if ($records = $query->get()) {
                $productsIds = array_column($records, 'id');
                $parentRestrictionId = $this->getParentRestrictionId();

                $structureManager = $this->getService('structureManager');
                $this->getService('ParametersManager')->preloadPrimaryParametersForProducts($productsIds);
                foreach ($productsIds as &$productId) {
                    if ($product = $structureManager->getElementById($productId, $parentRestrictionId)) {
                        $this->productsList[] = $product;
                    }
                }
            }
        }
        return $this->productsList;
    }

    protected function applyManualSorting($query, $productsIds)
    {
        $database = $this->getService('db');
        $sortDataQuery = $database->table('structure_links')
            ->select(('childStructureId'))->distinct()
            //do we need $this->getProductsListParentElementsIds() for parentStructureId?
            //this conflicts with case where both parent and child categories have products assigned.
            //->whereIn('parentStructureId', $this->getProductsListParentElementsIds())
            ->where('parentStructureId', '=', $this->id)
            ->whereIn('childStructureId', $productsIds)
            ->orderBy('position', 'asc');

        $sortIds = [];
        if ($records = $sortDataQuery->get()) {
            $sortIds = array_column($records, 'childStructureId');
        }
        if ($sortIds) {
            $query->orderByRaw($query->raw("FIELD(id, " . implode(',', $sortIds)) . ")");
        }
    }

    protected function getParentRestrictionId()
    {
        $languagesManager = $this->getService('languagesManager');;
        return $languagesManager->getCurrentLanguageId();
    }

    protected function getArgumentForFilter($argumentName)
    {
        $result = [];
        $value = controller::getInstance()->getParameter($argumentName);
        if ($value !== false) {
            if (strpos($value, ',') !== false) {
                $result = explode(',', $value);
            } else {
                $result = (array)$value;
            }
        }
        return $result;
    }

    public function parseSearchArguments()
    {
        if ($this->searchArguments === null) {
            $controller = controller::getInstance();
            $this->searchArguments = [
                'category' => $this->getArgumentForFilter('category'),
                'brand' => $this->getArgumentForFilter('brand'),
                'discount' => $this->getArgumentForFilter('discount'),
                'parameter' => $this->getArgumentForFilter('parameter'),
                'availability' => $this->getArgumentForFilter('availability'),
                'parametersValues' => [],
                'sort' => 'price',
                'order' => 'asc',
                'price' => [],
                'limit' => $this->getDefaultLimit(),
            ];
            $priceArgument = $controller->getParameter('price');
            if ($priceArgument && strpos($priceArgument, '-') !== false) {
                $this->searchArguments['price'] = explode('-', $priceArgument);
            }
            $parametersValues = [];
            if ($filtersString = $controller->getParameter('filters')) {
                $keyValuePairs = explode(';', $filtersString);
                foreach ($keyValuePairs as &$keyValuePair) {
                    $parameter = explode(',', $keyValuePair);
                    if (count($parameter) === 2) {
                        $parametersValues[$parameter[0]] = $parameter[1];
                    }
                }
            }
            $this->searchArguments['parametersValues'] = $parametersValues;
            if ($orderParameter = $controller->getParameter('sort')) {
                $order = explode(';', $orderParameter);
                $orderField = $order[0];
                if (in_array($orderField, [
                    'manual',
                    'title',
                    'price',
                    'date',
                    'brand',
                ])
                ) {
                    $this->searchArguments['sort'] = $orderField;
                    $this->searchArguments['order'] = (isset($order[1]) && $order[1] == 'desc') ? 'desc' : 'asc';
                }
            } else {
                if ($defaultOrder = $this->getDefaultOrder()) {
                    if (strpos($defaultOrder, ';') === false) {
                        $this->searchArguments['sort'] = $defaultOrder;
                    } else {
                        $defaultOrderParts = explode(';', $defaultOrder);
                        $this->searchArguments['sort'] = $defaultOrderParts[0];
                        $this->searchArguments['order'] = (isset($defaultOrderParts[1]) && $defaultOrderParts[1] == 'desc') ? 'desc' : 'asc';
                    }
                }
            }
            $limitArgument = $controller->getParameter('limit');
            if ((int)$limitArgument) {
                $this->searchArguments['limit'] = (int)$limitArgument;
            }
        }
        return $this->searchArguments;
    }

    public function getSearchArgumentParameterValue($parameterId)
    {
        $arguments = $this->parseSearchArguments();
        return isset($arguments['parametersValues'][$parameterId]) ? $arguments['parametersValues'][$parameterId] : '';
    }

    public function getCategoriesInfo()
    {
        $categoriesInfo = [];
        $availableProductsIds = $this->getConnectedProductsIds();

        $collection = persistableCollection::getInstance('structure_links');
        $conditions = [
            [
                'column' => 'type',
                'action' => '=',
                'argument' => 'catalogue',
            ],
            [
                'column' => 'childStructureId',
                'action' => 'in',
                'argument' => $availableProductsIds,
            ],
        ];
        $structureManager = $this->getService('structureManager');
        $categories = [];
        if ($records = $collection->conditionalLoad('parentStructureId', $conditions, [], [], ['parentStructureId'])
        ) {
            foreach ($records as &$record) {
                if ($category = $structureManager->getElementById($record['parentStructureId'])) {
                    $categories[] = $category;
                }
            }
        }

        if ($categories) {
            $mainCategoriesInfo = [];
            $subCategoriesInfo = [];
            foreach ($categories as &$category) {
                if ($parentCategory = $category->getMainParentCategory()) {
                    $mainCategoriesInfo[$parentCategory->id] = [
                        'title' => $parentCategory->title,
                        'id' => $parentCategory->id,
                    ];
                    $subCategoriesInfo[$parentCategory->id][] = [
                        'id' => $category->id,
                        'title' => $category->title,
                    ];
                } else {
                    $mainCategoriesInfo[$category->id] = [
                        'title' => $category->title,
                        'id' => $category->id,
                    ];
                }
            }
            $categoriesInfo = [
                'main' => $mainCategoriesInfo,
                'subs' => $subCategoriesInfo,
            ];
        }
        return $categoriesInfo;
    }

    public function getPriceRangeOptions()
    {
        if (null === $this->priceRangeOptions) {
            $this->priceRangeOptions = [];
            $productsIds = $this->getConnectedProductsIds();
            if ($this->priceInterval && $productsIds) {
                $collection = persistableCollection::getInstance('module_product');
                $conditions = [
                    [
                        'id',
                        'IN',
                        $productsIds,
                    ],
                ];

                if ($records = $collection->conditionalLoad('distinct(price)', $conditions, ['price' => 'asc'], [], [], true)
                ) {
                    $prices = [];
                    foreach ($records as &$record) {
                        $prices[] = $record['price'];
                    }
                    $priceCount = count($prices);

                    $priceChunks = array_chunk($prices, max(ceil($priceCount / $this->priceInterval), 2));

                    foreach ($priceChunks as $priceChunk) {
                        $this->priceRangeOptions[] = [
                            $priceChunk[0],
                            array_pop($priceChunk),
                        ];
                    }
                }
            }
        }
        return $this->priceRangeOptions;
    }

    public function getCurrencyPriceLabel($price)
    {
        $currencySelector = $this->getService('CurrencySelector');
        return sprintf('%01.2f', $currencySelector->convertPrice($price)) . ' ' . $currencySelector->getSelectedCurrencyItem()->symbol;
    }

    public function isFilterable()
    {
        if (!$this->getProductsListBaseIds()) {
            return false;
        }
        if ($this->isFilterableByPrice() || $this->isFilterableByCategory() || $this->isFilterableByDiscount() || $this->isFilterableByBrand() || $this->isFilterableByParameter() || $this->isFilterableByAvailability()
        ) {
            return true;
        }
        return false;
    }

    public function isFilterableByBrand()
    {
        return false;
    }

    public function isFilterableByDiscount()
    {
        return false;
    }

    public function isFilterableByAvailability()
    {
        return false;
    }

    public function isFilterableByCategory()
    {
        return false;
    }

    public function isFilterableByParameter()
    {
        return false;
    }

    public function isFilterableByPrice()
    {
        return false;
    }

    protected function generatePagerUrl(array $searchArguments)
    {
        $url = $this->getFilteredUrl();
        if ($searchArguments['sort']) {
            $url .= 'sort:' . $searchArguments['sort'];
        }
        if ($searchArguments['order']) {
            $url .= ';' . $searchArguments['order'];
        }
        $url .= '/';
        return $url;
    }

    public function getFilteredUrl()
    {
        $controller = controller::getInstance();
        $url = $controller->pathURL;
        foreach ($controller->getParameters() as $key => $value) {
            if (!is_array($value) && $key !== 'sort' && $value !== '') {
                $url .= $key . ':' . $value . '/';
            }
        }
        return $url;
    }

    protected function getProductsListParentElementsIds()
    {
        return (array)$this->id;
    }

    /**
     * Returns all brands connected to the products (!) in this category
     * @return brandElement[]
     */
    public function getBrandsList()
    {
        if (is_null($this->brandsList)) {
            $this->brandsList = [];
            if ($productIds = $this->getConnectedProductsIds()) {
                $collection = persistableCollection::getInstance("module_product");

                // filter out products that are out of stock/unavailable
                $conditions = [
                    [
                        "availability",
                        "=",
                        "quantity_dependent",
                    ],
                    [
                        "quantity",
                        "=",
                        "0",
                    ],
                ];

                if ($records = $collection->conditionalLoad("id", $conditions)) {
                    $unavailableProductsIds = [];
                    foreach ($records as &$record) {
                        $unavailableProductsIds[] = $record["id"];
                    }
                    $productIds = array_diff($productIds, $unavailableProductsIds);
                }

                if ($productIds) {
                    $conditions = [
                        [
                            "availability",
                            "!=",
                            "unavailable",
                        ],
                        [
                            "id",
                            "in",
                            $productIds,
                        ],
                    ];
                    if ($records = $collection->conditionalLoad("id", $conditions)) {
                        $productIds = [];
                        foreach ($records as &$record) {
                            $productIds[] = $record["id"];
                        }
                    }

                    // now find the brands
                    if ($productIds) {
                        $collection = persistableCollection::getInstance("structure_links");
                        $conditions = [
                            [
                                'column' => 'childStructureId',
                                'action' => 'in',
                                'argument' => $productIds,
                            ],
                            [
                                'column' => 'type',
                                'action' => '=',
                                'argument' => "productbrand",
                            ],
                        ];
                        if ($records = $collection->conditionalLoad('parentStructureId', $conditions, [], [], ['parentStructureId'])
                        ) {
                            $sort = [];
                            $structureManager = $this->getService('structureManager');
                            foreach ($records as &$record) {
                                if ($brand = $structureManager->getElementById($record["parentStructureId"])) {
                                    $sort[] = mb_strtolower($brand->title);
                                    $this->brandsList[] = $brand;
                                }
                            }
                            array_multisort($sort, SORT_ASC, $this->brandsList);
                        }
                    }
                }
            }
        }
        return $this->brandsList;
    }

    public function getDiscountsList()
    {
        if ($this->discountsList === null) {
            $this->discountsList = [];

            if ($productIds = $this->getConnectedProductsIds()) {
                $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
                $discountsList = $shoppingBasketDiscounts->getApplicableDiscountsList();
                foreach ($discountsList as &$discount) {
                    if ($discount->checkProductsListIfApplicable($productIds)) {
                        $this->discountsList[] = $discount;
                    }
                }
            }
        }
        return $this->discountsList;
    }

    public function getSelectedSortParameter()
    {
        if (is_null($this->selectedSortParameter)) {
            $this->selectedSortParameter = "";
            $controller = controller::getInstance();
            if ($parameterFromUrl = $controller->getParameter('sort')) {
                $this->selectedSortParameter = $parameterFromUrl;
            }
        }
        return $this->selectedSortParameter;
    }

    public function isSortable()
    {
        $parameters = $this->getSortingOptions();
        if ($parameters && count($parameters) > 1) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     * @deprecated
     */
    public function getSortParameters()
    {
        $this->logError('Deprecated method getSortParameters used, use getSortingOptions');
        if (is_null($this->sortParameters)) {
            $arguments = $this->parseSearchArguments();
            $filteredUrl = $this->getFilteredUrl();
            $this->sortParameters = [];

            $this->sortParameters['manual'] = [
                'url' => $filteredUrl,
                'active' => false,
                'reversable' => false,
            ];

            if ($this->priceSortingEnabled) {
                $this->sortParameters['price'] = [
                    'url' => $filteredUrl . 'sort:price/',
                    'active' => false,
                    'reversable' => true,
                ];
            }
            if ($this->nameSortingEnabled) {
                $this->sortParameters['title'] = [
                    'url' => $filteredUrl . 'sort:title/',
                    'active' => false,
                    'reversable' => true,
                ];
            }
            if ($this->dateSortingEnabled) {
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

    public function getSortingOptions()
    {
        if ($this->sortingOptions === null) {
            $this->sortingOptions = [];
            $arguments = $this->parseSearchArguments();
            $activeSortArgument = $arguments['sort'];
            $activeOrderArgument = $arguments['order'];
            $translationsManager = $this->getService('translationsManager');
            $filteredUrl = $this->getFilteredUrl();
            if ($this->isFieldSortable('manual')) {
                $this->sortingOptions[] = [
                    'url' => $filteredUrl,
                    'active' => $activeSortArgument == 'manual',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_manual'),
                ];
            }
            if ($this->isFieldSortable('price')) {
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:price/',
                    'active' => $activeSortArgument == 'price' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_price'),
                ];
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:price;desc/',
                    'active' => $activeSortArgument == 'price' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_price_desc'),
                ];
            }
            if ($this->isFieldSortable('title')) {
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:title/',
                    'active' => $activeSortArgument == 'title' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_title'),
                ];
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:title;desc/',
                    'active' => $activeSortArgument == 'title' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_title_desc'),
                ];
            }
            if ($this->isFieldSortable('date')) {
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:date/',
                    'active' => $activeSortArgument == 'date' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_date'),
                ];
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:date;desc/',
                    'active' => $activeSortArgument == 'date' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_date_desc'),
                ];
            }
            if ($this->isFieldSortable('brand')) {
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:brand/',
                    'active' => $activeSortArgument == 'brand' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_brand'),
                ];
                $this->sortingOptions[] = [
                    'url' => $filteredUrl . 'sort:brand;desc/',
                    'active' => $activeSortArgument == 'brand' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_brand_desc'),
                ];
            }
        }
        return $this->sortingOptions;
    }

    protected function isFieldSortable($field)
    {
        switch ($field) {
            case 'manual':
                return true;
            case 'price':
                return $this->priceSortingEnabled;
            case 'title':
                return $this->nameSortingEnabled;
            case 'date':
                return $this->dateSortingEnabled;
            case 'brand':
                return $this->brandSortingEnabled;
        }
        return false;
    }

    /**
     * @return array
     * @deprecated
     */
    public function getProductSelectionFilters()
    {
        $this->logError('deprecated method getProductSelectionFilters used');
        return [];
    }

    public function getProductsPager()
    {
        if (!$this->productsPager) {
            $this->getProductsList();
        }
        return $this->productsPager;
    }

    public function getAllSelectionParametersIds()
    {
    }

    protected function getSelectionIdsForFiltering()
    {
        if ($this->selectionsIdsForFiltering === null) {
            $this->selectionsIdsForFiltering = [];
            $conditions = [];
            $collection = persistableCollection::getInstance('module_product_selection');
            $records = $collection->conditionalLoad('distinct(id)', $conditions, [], [], [], true);
            if ($records) {
                foreach ($records as &$record) {
                    $this->selectionsIdsForFiltering[] = $record['id'];
                }
            }
        }
        return $this->selectionsIdsForFiltering;
    }

    protected function getParametersForFiltering()
    {
        $selections = [];
        if ($selectionsIds = $this->getSelectionIdsForFiltering()) {
            $structureManager = $this->getService('structureManager');
            $selectionsValuesIndex = $this->getSelectionsValuesIndex($selectionsIds);
            foreach ($selectionsValuesIndex as $selectionId => &$selectionValuesIds) {
                $selectionElement = false;
                $elements = $structureManager->getElementsByIdList((array)$selectionId, $this->id);
                if ($elements) {
                    $selectionElement = reset($elements);
                }
                if ($selectionElement) {
                    $selectionValuesIds = array_unique($selectionValuesIds);
                    $selections[] = [
                        'selection' => $selectionElement,
                        'options' => $selectionValuesIds,
                    ];
                }
            }
        }
        return $selections;
    }

    public function getFilterSelections()
    {
        $selections = [];
        if ($selectionsIds = $this->getSelectionIdsForFiltering()) {
            $structureManager = $this->getService('structureManager');
            $selectionsValuesIndex = $this->getSelectionsValuesIndex($selectionsIds);
            foreach ($selectionsValuesIndex as $selectionId => &$selectionValuesIds) {
                $selectionElement = false;
                $elements = $structureManager->getElementsByIdList((array)$selectionId, $this->id);
                if ($elements) {
                    $selectionElement = reset($elements);
                }
                if ($selectionElement) {
                    $selectionValuesIds = array_unique($selectionValuesIds);
                    $selections[] = [
                        'selection' => $selectionElement,
                        'options' => $structureManager->getElementsByIdList($selectionValuesIds),
                    ];
                }
            }
        }
        return $selections;
    }

    protected function getSelectionsValuesIndex($selectionsIds)
    {
        $productSelectionsValues = [];
        $productIds = $this->getProductsListBaseIds();

        $collection = persistableCollection::getInstance('module_product_parameter_value');
        $conditions = [
            [
                'productId',
                'IN',
                $productIds,
            ],
            [
                'parameterId',
                'IN',
                $selectionsIds,
            ],
        ];
        $records = $collection->conditionalLoad([
            'parameterId',
            'value',
        ], $conditions);
        if ($records) {
            foreach ($records as &$record) {
                if (!isset($productSelectionsValues[$record['parameterId']])) {
                    $productSelectionsValues[$record['parameterId']] = [];
                }
                $productSelectionsValues[$record['parameterId']][] = $record['value'];
            }
        }
        return $productSelectionsValues;
    }

    public function getCanonicalUrl()
    {
        $canonicalUrl = parent::getCanonicalUrl();
        if ($pager = $this->getProductsPager()) {
            if ($pager->getCurrentPage() > 1) {
                $canonicalUrl .= 'page:' . $pager->getCurrentPage() . '/';
            }
        }

        return $canonicalUrl;
    }

    public function getResidingProducts($productId)
    {
        $result = ['previous' => false, 'next' => false];
        if ($productsIds = $this->getProductsListBaseIds()) {
            $structureManager = $this->getService('structureManager');
            $database = $this->getService('db');
            $query = $database->table('module_product')
                ->select(('id'))
                ->whereIn('id', $productsIds)
                ->groupBy('id');

            $this->applyManualSorting($query, $productsIds);
            if ($records = $query->get('id')) {
                $productsIds = array_column($records, 'id');
            }

            if (($key = array_search($productId, $productsIds)) !== false) {
                if ($key > 0) {
                    if ($previousId = $productsIds[$key - 1]) {
                        $result['previous'] = $structureManager->getElementById($previousId);
                    }
                }
                if ($key < count($productsIds) - 1) {
                    if ($nextId = $productsIds[$key + 1]) {
                        $result['next'] = $structureManager->getElementById($nextId);
                    }
                }
            }
        }
        return $result;
    }

    public function getDefaultLimit()
    {
        return $this->getService('ConfigManager')->get('main.pageAmountProducts');
    }

    /*
     * method isSettingEnabled for category page, catalogue
     * attribute $this->amountOnPageEnabled for brand page, selected products, discount
    */
    public function getLimits()
    {
        if (is_null($this->limits)) {
            $displayLimit = false;
            if (method_exists($this, 'isSettingEnabled')) {
                if ($this->isSettingEnabled('amountOnPageEnabled')) {
                    $displayLimit = true;
                }
            } elseif ($this->amountOnPageEnabled) {
                $displayLimit = true;
            }

            if ($displayLimit) {
                $arguments = $this->parseSearchArguments();
                $controller = controller::getInstance();
                $url = $controller->pathURL;
                foreach ($controller->getParameters() as $key => $value) {
                    if (!is_array($value) && $key !== 'page' && $key !== 'limit' && $value !== '') {
                        $url .= $key . ':' . $value . '/';
                    }
                }

                $this->limits = [];
                $defaultLimit = $this->getDefaultLimit();
                $this->limits[$defaultLimit] = [
                    'url' => $url,
                    'selected' => false,
                ];
                foreach ($this->getService('ConfigManager')->get('main.availablePageAmountProducts') as $limit) {
                    $this->limits[$limit] = [
                        'url' => $url . 'limit:' . $limit . '/',
                        'selected' => false,
                    ];
                }
                if (isset($this->limits[$arguments['limit']])) {
                    $this->limits[$arguments['limit']]['selected'] = true;
                }
            }
        }
        return $this->limits;
    }

    public function getUsedParametersInfo()
    {
        if ($this->usedParametersInfo !== null) {
            return $this->usedParametersInfo;
        }
        $this->usedParametersInfo = [];
        if ($usedParametersIndex = $this->getUsedParametersInfoIndex()) {
            if ($connectedParameterIds = $this->getParametersIdList()) {
                $connectedParameterIndex = array_flip($connectedParameterIds);
                foreach ($usedParametersIndex as $parameterId => $parameterInfo) {
                    if (isset($connectedParameterIndex[$parameterId])) {
                        $this->usedParametersInfo[] = $parameterInfo;
                    }
                }
            }
        }
        return $this->usedParametersInfo;
    }

    public function getParametersIdList()
    {
        $linksManager = $this->getService('linksManager');
        return $linksManager->getConnectedIdList($this->id, $this->structureType . 'Parameter', 'parent');
    }

    protected function getUsedParametersInfoIndex()
    {
        if (!is_null($this->usedParametersInfoIndex)) {
            return $this->usedParametersInfoIndex;
        }
        $this->usedParametersInfoIndex = [];
        if ($products = $this->getProductsList()) {
            foreach ($products as &$product) {
                foreach ($product->getPrimaryParametersInfo() as $parameterInfo) {
                    if (!isset($this->usedParametersInfoIndex[$parameterInfo['id']])) {
                        $this->usedParametersInfoIndex[$parameterInfo['id']] = $parameterInfo;
                    }
                }
            }
        }
        return $this->usedParametersInfoIndex;
    }

    public function getActiveTableColumns()
    {
        $info = [
            'title' => false,
            'code' => false,
            'unit' => false,
            'minimumOrder' => false,
            'availability' => true,
            'price' => false,
            'discount' => false,
            'quantity' => false,
            'basket' => false,
            'view' => true,
        ];
        if ($products = $this->getProductsList()) {
            foreach ($products as $product) {
                if ($product->title) {
                    $info['title'] = true;
                }
                if ($product->code != $product->id) {
                    $info['code'] = true;
                }
                if ($product->getUnit()) {
                    $info['unit'] = true;
                }
                if ($product->minimumOrder > 1) {
                    $info['minimumOrder'] = true;
                }
                if (!$product->isEmptyPrice()) {
                    $info['price'] = true;
                }
                if ($product->getOldPrice()) {
                    $info['discount'] = true;
                }
                if ($product->isPurchasable() && !$product->isBasketSelectionRequired()) {
                    $info['quantity'] = true;
                    $info['basket'] = true;
                }
            }
        }
        return $info;
    }
}