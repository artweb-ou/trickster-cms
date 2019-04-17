<?php

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

abstract class ProductsListStructureElement extends menuStructureElement
{
    use ProductFilterFactoryTrait;

//    protected $brandFiltered = true;
//    protected $discountFiltered = true;
//    protected $parameterFiltered = true;
    protected $productsList;

    protected $productsListBaseQuery;
    protected $productsListBaseAmount;

    protected $filteredProductsQuery;
    protected $filteredProductsAmount;
    /**
     * @var pager
     */
    protected $productsPager;
//    protected $brandsList;
//    protected $discountsList;
//    protected $connectedProductsIds;
//    protected $selectedBrandsIdList;
//    protected $selectedFilterValues;
//    protected $selectedSortParameter;
//    protected $selectedDiscountId;
//    protected $productSelectionFilters;
//    protected $sortParameters;
    protected $amountSelectionOptions;
    protected $filterArguments;
//    protected $priceRangeOptions;
//    /**@var productFilter */
//    protected $baseFilter;
    protected $filters;
    protected $filtersIndex = [];
//    protected $productSearchFilterTypes;
    protected $selectionsIdsForFiltering;
    protected $sortingOptions;
//    protected $usedParametersInfo;
//    protected $usedParametersInfoIndex;

//    public function getFiltersByType($type)
//    {
//        return isset($this->filtersIndex[$type]) ? $this->filtersIndex[$type] : [];
//    }
//
//    public function getFiltersIndex()
//    {
//        return $this->filtersIndex;
//    }

    protected $filterCategoryIds;
    protected $filterBrandIds;
    protected $filterDiscountIds;
    protected $filterParameterIds;
    protected $filterParameterValues;
    protected $filterAvailability;
    protected $filterSort;
    protected $filterOrder;
    protected $filterPrice;
    protected $filterLimit;

    public function getFilters()
    {
        if ($this->filters === null) {
            $this->filters = [];
            if ($this->isFilterable()) {
                $filterTypes = ['category', 'brand', 'discount', 'availability', 'parameter', 'price'];
                foreach ($filterTypes as $filterType) {
                    if ($this->isFilterableByType($filterType)) {
                        $this->addFilter($this->createProductFilter($filterType));
                    }
                }
//                if ($this->isFilterableByType('category')) {
//                    $this->addFilter($this->createProductFilter('category'));
////                if ($categoriesIds = $this->getTopLevelCategoriesIds()) {
////                }
//                }
//                if ($this->isFilterableByType('brand')) {
//                    $this->addFilter($this->createProductFilter('brand'));
//                }
//                if ($this->isFilterableByType('discount')) {
//                    $this->addFilter($this->createProductFilter('discount'));
//
////                $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
////                $discountsList = $shoppingBasketDiscounts->getApplicableDiscountsList();
////                if ($discountsList) {
////                    $filter = $this->createProductFilter('discount', $arguments['discount']);
////                    $this->addFilter($filter);
////                }
//                }
//                if ($this->isFilterableByType('availability')) {
//                    $this->addFilter($this->createProductFilter('availability'));
//                }
//                if ($this->isFilterableByType('parameter')) {
//                    $this->addFilter($this->createProductFilter('parameter'));
//
////                $selections = $this->getParametersForFiltering();
////                foreach ($selections as &$selectionInfo) {
////                    $filter = $this->createProductFilter('parameter', array_intersect($arguments['parameter'], $selectionInfo['options']), $selectionInfo['options']);
////                    $filter->setSelectionId($selectionInfo['selection']->id);
////                    $this->addFilter($filter);
////                }
//                }
//                if ($this->isFilterableByType('price')) {
//                    $this->addFilter($this->createProductFilter('price'));
////                $filter = $this->createProductFilter('price', $arguments['price']);
////                $structureManager = $this->getService('structureManager');
////                $languageId = $this->getService('languagesManager')->getCurrentLanguageId();
////                $elements = $structureManager->getElementsByType('productSearch', $languageId);
////                if ($elements) {
////                    $productSearchElement = $elements[0];
////                    $filter->setRangeInterval($productSearchElement->priceInterval);
////                }
////                $this->addFilter($filter);
//                }
            }
        }
        return $this->filters;
    }

    public function addFilter(productFilter $filter)
    {
        $this->filters[] = $filter;
        $type = $filter->getType();
        if (!isset($this->filtersIndex[$type])) {
            $this->filtersIndex[$type] = [];
        }
        $this->filtersIndex[$type][] = $filter;

    }

    public function getDefaultOrder()
    {
        return !empty($this->defaultOrder) ? $this->defaultOrder : 'price';
    }

    protected function getProductsQuery()
    {
        /**
         * @var Connection $db
         */
        $db = $this->getService('db');

        //basic query to get all non-hidden products available in stock
        $query = $db->table('module_product');
        $query->select('module_product.id')->distinct();
        $query->where(function (Builder $query) {
            $query->where('inactive', '!=', '1');
            $query->where(function (Builder $query) {
                $query->orWhere('availability', '!=', 'unavailable');
                $query->orWhere(function (Builder $query) {
                    $query->where('availability', '=', 'quantity_dependent');
                    $query->where('quantity', '!=', 0);
                });
            });
        });
        return $query;
    }

    protected function getFilteredProductsQuery()
    {
        //todo: cache it
        if ($this->filteredProductsQuery === null) {
            $this->filteredProductsQuery = [];
            if ($productsListBaseQuery = $this->getProductsListBaseQuery()) {
                $this->filteredProductsQuery = clone $productsListBaseQuery;
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                if ($categoryIds = $this->getFilterCategoryIds()) {
                    $deepCategoryIds = [];

                    foreach ($categoryIds as $categoryId) {
                        if ($category = $structureManager->getElementById($categoryId)) {
                            $category->gatherSubCategoriesIdIndex($categoryId, $deepCategoryIds);
                        }
                    }
                    if ($deepCategoryIds) {
                        $deepCategoryIds = array_keys($deepCategoryIds);
                        $this->filteredProductsQuery->whereIn('module_product.id', function ($query) use ($deepCategoryIds) {
                            $query->from('structure_links')
                                ->select('childStructureId')
                                ->whereIn('parentStructureId', $deepCategoryIds)
                                ->where('type', '=', 'catalogue');
                        });
                    }

                }
                if ($brandsIds = $this->getFilterBrandIds()) {
                    $this->filteredProductsQuery->whereIn('module_product.brandId', $brandsIds);
                }
            }
        }

        return $this->filteredProductsQuery;
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

        if ($filteredProductsQuery = $this->getFilteredProductsQuery()) {
            $elementsOnPage = $this->getFilterLimit();

            $sort = $this->getFilterSort();
            $order = $this->getFilterOrder();
            if ($sort && $sort == 'manual') {
                $this->applyManualSorting($filteredProductsQuery, $filteredProductsQuery);
            } elseif ($sort == 'date') {
                $filteredProductsQuery->join('structure_elements', 'module_product.id', '=', 'structure_elements.id', 'left');
                $filteredProductsQuery->orderBy('structure_elements.dateCreated', $order);
            } elseif ($sort == 'brand') {
                $filteredProductsQuery->join('module_brand', 'module_product.brandId', '=', 'module_brand.id', 'left');
                $filteredProductsQuery->orderBy('module_brand.title', $order);
            } else {
                $filteredProductsQuery->orderBy($sort, $order);
            }

            $pager = new pager($this->generatePagerUrl(), $this->getFilteredProductsAmount(), $elementsOnPage, (int)controller::getInstance()
                ->getParameter('page'), "page");
            $this->productsPager = $pager;

            $filteredProductsQuery->skip($pager->startElement)->take($elementsOnPage)->groupBy('module_product.id');
            if ($records = $filteredProductsQuery->get()) {
                $productIds = array_column($records, 'id');
                $parentRestrictionId = $this->getProductsListParentRestrictionId();
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                $this->getService('ParametersManager')->preloadPrimaryParametersForProducts($productIds);
                foreach ($productIds as &$productId) {
                    if ($product = $structureManager->getElementById($productId, $parentRestrictionId)) {
                        $this->productsList[] = $product;
                    }
                }
            }
        }
        return $this->productsList;
    }

    public function getFilteredProductsAmount()
    {
        if ($this->filteredProductsAmount === null) {
            if ($query = $this->getFilteredProductsQuery()) {
                $this->filteredProductsAmount = $query->count('module_product.id');
            }
        }
        return $this->filteredProductsAmount;
    }

    public function getProductsListBaseAmount()
    {
        if ($this->productsListBaseAmount === null) {
            if ($query = $this->getProductsListBaseQuery()) {
                $this->productsListBaseAmount = $query->count('module_product.id');
            }
        }
        return $this->productsListBaseAmount;
    }

    /**
     * @param Builder $query
     * @param int[] $productsIds
     */
    protected function applyManualSorting($query, $productsIds)
    {
        /**
         * @var Connection $db
         */
        $db = $this->getService('db');
        $sortDataQuery = $db->table('structure_links')
            ->select(('childStructureId'))->distinct()
            ->where('parentStructureId', '=', $this->id)
            ->whereIn('childStructureId', $productsIds)
            ->orderBy('position', 'asc');

        $sortIds = [];
        if ($records = $sortDataQuery->get()) {
            $sortIds = array_column($records, 'childStructureId');
        }
        if ($sortIds) {
            $query->orderByRaw($query->raw("FIELD(engine_module_product.id, " . implode(',', $sortIds)) . ")");
        }
    }

    protected function getProductsListParentRestrictionId()
    {
        $languagesManager = $this->getService('languagesManager');;
        return $languagesManager->getCurrentLanguageId();
    }

    protected function getArgumentForFilter($argumentName)
    {
        $result = [];
        if ($value = controller::getInstance()->getParameter($argumentName)) {
            $result = explode(',', $value);
        }
        return $result;
    }

    /**
     * @return int[]
     */
    public function getFilterCategoryIds()
    {
        if ($this->filterCategoryIds === null) {
            $this->filterCategoryIds = $this->getArgumentForFilter('category');
        }
        return $this->filterCategoryIds;
    }

    /**
     * @return int[]
     */
    public function getFilterBrandIds()
    {
        if ($this->filterBrandIds === null) {
            $this->filterBrandIds = $this->getArgumentForFilter('brand');
        }
        return $this->filterBrandIds;
    }

    /**
     * @return int[]
     */
    public function getFilterDiscountIds()
    {
        if ($this->filterDiscountIds === null) {
            $this->filterDiscountIds = $this->getArgumentForFilter('discount');
        }
        return $this->filterDiscountIds;
    }

    /**
     * @return int[]
     */
    public function getFilterParameterIds()
    {
        if ($this->filterParameterIds === null) {
            $this->filterParameterIds = $this->getArgumentForFilter('parameter');
        }
        return $this->filterParameterIds;
    }

    /**
     * @return int[]
     */
    public function getFilterParameterValues()
    {
        if ($this->filterParameterValues === null) {
            $this->filterParameterValues = [];
            $controller = controller::getInstance();
            if ($filtersString = $controller->getParameter('filters')) {
                $keyValuePairs = explode(';', $filtersString);
                foreach ($keyValuePairs as $keyValuePair) {
                    $parameter = explode(',', $keyValuePair);
                    if (count($parameter) === 2) {
                        $this->filterParameterValues[$parameter[0]] = $parameter[1];
                    }
                }
            }
        }
        return $this->filterParameterValues;
    }

    /**
     * @return mixed
     */
    public function getFilterAvailability()
    {
        if ($this->filterAvailability === null) {
            $this->filterAvailability = $this->getArgumentForFilter('availability');
        }
        return $this->filterAvailability;
    }

    /**
     * @return mixed
     */
    public function getFilterSort()
    {
        $this->getFilterOrder();
        return $this->filterSort;
    }

    /**
     * @return mixed
     */
    public function getFilterOrder()
    {
        if ($this->filterOrder === null) {
            $controller = controller::getInstance();
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
                    $this->filterSort = $orderField;
                    $this->filterOrder = (isset($order[1]) && $order[1] == 'desc') ? 'desc' : 'asc';
                }
            } elseif ($defaultOrder = $this->getDefaultOrder()) {
                if (strpos($defaultOrder, ';') === false) {
                    $this->filterSort = $defaultOrder;
                } else {
                    $defaultOrderParts = explode(';', $defaultOrder);
                    $this->filterSort = $defaultOrderParts[0];
                    $this->filterOrder = (isset($defaultOrderParts[1]) && $defaultOrderParts[1] == 'desc') ? 'desc' : 'asc';
                }
            }

        }

        return $this->filterOrder;
    }

    /**
     * @return float[]
     */
    public function getFilterPrice()
    {
        if ($this->filterPrice === null) {
            $controller = controller::getInstance();
            $priceArgument = $controller->getParameter('price');
            if ($priceArgument && strpos($priceArgument, '-') !== false) {
                $this->filterPrice = explode('-', $priceArgument);
            }

        }
        return $this->filterPrice;
    }

    /**
     * @return mixed
     */
    public function getFilterLimit()
    {
        if ($this->filterLimit === null) {
            $controller = controller::getInstance();
            $limitArgument = $controller->getParameter('limit');
            if ((int)$limitArgument) {
                $this->filterLimit = (int)$limitArgument;
            } else {
                $this->filterLimit = $this->getDefaultLimit();
            }
        }
        return $this->filterLimit;
    }

    /**
     * @deprecated
     */
    public function getFilterArguments()
    {
        return $this->filterArguments;
    }

//    public function getSearchArgumentParameterValue($parameterId)
//    {
//        $arguments = $this->getFilterArguments();
//        return isset($arguments['parametersValues'][$parameterId]) ? $arguments['parametersValues'][$parameterId] : '';
//    }

//    public function getCategoriesInfo()
//    {
//        $categoriesInfo = [];
//        $availableProductsIds = $this->getConnectedProductsIds();
//
//        $collection = persistableCollection::getInstance('structure_links');
//        $conditions = [
//            [
//                'column' => 'type',
//                'action' => '=',
//                'argument' => 'catalogue',
//            ],
//            [
//                'column' => 'childStructureId',
//                'action' => 'in',
//                'argument' => $availableProductsIds,
//            ],
//        ];
//        $structureManager = $this->getService('structureManager');
//        $categories = [];
//        if ($records = $collection->conditionalLoad('parentStructureId', $conditions, [], [], ['parentStructureId'])
//        ) {
//            foreach ($records as &$record) {
//                if ($category = $structureManager->getElementById($record['parentStructureId'])) {
//                    $categories[] = $category;
//                }
//            }
//        }
//
//        if ($categories) {
//            $mainCategoriesInfo = [];
//            $subCategoriesInfo = [];
//            foreach ($categories as &$category) {
//                if ($parentCategory = $category->getMainParentCategory()) {
//                    $mainCategoriesInfo[$parentCategory->id] = [
//                        'title' => $parentCategory->title,
//                        'id' => $parentCategory->id,
//                    ];
//                    $subCategoriesInfo[$parentCategory->id][] = [
//                        'id' => $category->id,
//                        'title' => $category->title,
//                    ];
//                } else {
//                    $mainCategoriesInfo[$category->id] = [
//                        'title' => $category->title,
//                        'id' => $category->id,
//                    ];
//                }
//            }
//            $categoriesInfo = [
//                'main' => $mainCategoriesInfo,
//                'subs' => $subCategoriesInfo,
//            ];
//        }
//        return $categoriesInfo;
//    }

//    public function getPriceRangeOptions()
//    {
//        if (null === $this->priceRangeOptions) {
//            $this->priceRangeOptions = [];
//            $productsIds = $this->getConnectedProductsIds();
//            if ($this->priceInterval && $productsIds) {
//                $collection = persistableCollection::getInstance('module_product');
//                $conditions = [
//                    [
//                        'id',
//                        'IN',
//                        $productsIds,
//                    ],
//                ];
//
//                if ($records = $collection->conditionalLoad('distinct(price)', $conditions, ['price' => 'asc'], [], [], true)
//                ) {
//                    $prices = [];
//                    foreach ($records as &$record) {
//                        $prices[] = $record['price'];
//                    }
//                    $priceCount = count($prices);
//
//                    $priceChunks = array_chunk($prices, max(ceil($priceCount / $this->priceInterval), 2));
//
//                    foreach ($priceChunks as $priceChunk) {
//                        $this->priceRangeOptions[] = [
//                            $priceChunk[0],
//                            array_pop($priceChunk),
//                        ];
//                    }
//                }
//            }
//        }
//        return $this->priceRangeOptions;
//    }

//    public function getCurrencyPriceLabel($price)
//    {
//        $currencySelector = $this->getService('CurrencySelector');
//        return sprintf('%01.2f', $currencySelector->convertPrice($price)) . ' ' . $currencySelector->getSelectedCurrencyItem()->symbol;
//    }

    protected function isFilterable()
    {
        if (!$this->getProductsListBaseAmount()) {
            return false;
        }
        return true;
    }

    protected function generatePagerUrl()
    {
        $url = $this->getFilteredUrl();
        if ($sort = $this->getFilterSort()) {
            $url .= 'sort:' . $sort;
        }
        if ($order = $this->getFilterOrder()) {
            $url .= ';' . $order;
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

//    /**
//     * Returns all brands connected to the products (!) in this category
//     * @return brandElement[]
//     */
//    public function getBrandsList()
//    {
//        if (is_null($this->brandsList)) {
//            $this->brandsList = [];
//            if ($productIds = $this->getConnectedProductsIds()) {
//                $collection = persistableCollection::getInstance("module_product");
//
//                // filter out products that are out of stock/unavailable
//                $conditions = [
//                    [
//                        "availability",
//                        "=",
//                        "quantity_dependent",
//                    ],
//                    [
//                        "quantity",
//                        "=",
//                        "0",
//                    ],
//                ];
//
//                if ($records = $collection->conditionalLoad("id", $conditions)) {
//                    $unavailableProductsIds = [];
//                    foreach ($records as &$record) {
//                        $unavailableProductsIds[] = $record["id"];
//                    }
//                    $productIds = array_diff($productIds, $unavailableProductsIds);
//                }
//
//                if ($productIds) {
//                    $conditions = [
//                        [
//                            "availability",
//                            "!=",
//                            "unavailable",
//                        ],
//                        [
//                            "id",
//                            "in",
//                            $productIds,
//                        ],
//                    ];
//                    if ($records = $collection->conditionalLoad("id", $conditions)) {
//                        $productIds = [];
//                        foreach ($records as &$record) {
//                            $productIds[] = $record["id"];
//                        }
//                    }
//
//                    // now find the brands
//                    if ($productIds) {
//                        $collection = persistableCollection::getInstance("structure_links");
//                        $conditions = [
//                            [
//                                'column' => 'childStructureId',
//                                'action' => 'in',
//                                'argument' => $productIds,
//                            ],
//                            [
//                                'column' => 'type',
//                                'action' => '=',
//                                'argument' => "productbrand",
//                            ],
//                        ];
//                        if ($records = $collection->conditionalLoad('parentStructureId', $conditions, [], [], ['parentStructureId'])
//                        ) {
//                            $sort = [];
//                            $structureManager = $this->getService('structureManager');
//                            foreach ($records as &$record) {
//                                if ($brand = $structureManager->getElementById($record["parentStructureId"])) {
//                                    $sort[] = mb_strtolower($brand->title);
//                                    $this->brandsList[] = $brand;
//                                }
//                            }
//                            array_multisort($sort, SORT_ASC, $this->brandsList);
//                        }
//                    }
//                }
//            }
//        }
//        return $this->brandsList;
//    }
//
//    public function getDiscountsList()
//    {
//        if ($this->discountsList === null) {
//            $this->discountsList = [];
//
//            if ($productIds = $this->getConnectedProductsIds()) {
//                $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
//                $discountsList = $shoppingBasketDiscounts->getApplicableDiscountsList();
//                foreach ($discountsList as &$discount) {
//                    if ($discount->checkProductsListIfApplicable($productIds)) {
//                        $this->discountsList[] = $discount;
//                    }
//                }
//            }
//        }
//        return $this->discountsList;
//    }
//
//    public function getSelectedSortParameter()
//    {
//        if (is_null($this->selectedSortParameter)) {
//            $this->selectedSortParameter = "";
//            $controller = controller::getInstance();
//            if ($parameterFromUrl = $controller->getParameter('sort')) {
//                $this->selectedSortParameter = $parameterFromUrl;
//            }
//        }
//        return $this->selectedSortParameter;
//    }
//
    /**
     * @return bool
     */
    public function isSortable()
    {
        $parameters = $this->getSortingOptions();
        if ($parameters && count($parameters) > 1) {
            return true;
        }
        return false;
    }
//
//    /**
//     * @return array
//     * @deprecated
//     */
//    public function getSortParameters()
//    {
//        $this->logError('Deprecated method getSortParameters used, use getSortingOptions');
//        if (is_null($this->sortParameters)) {
//            $arguments = $this->getFilterArguments();
//            $filteredUrl = $this->getFilteredUrl();
//            $this->sortParameters = [];
//
//            $this->sortParameters['manual'] = [
//                'url' => $filteredUrl,
//                'active' => false,
//                'reversable' => false,
//            ];
//
//            if ($this->priceSortingEnabled) {
//                $this->sortParameters['price'] = [
//                    'url' => $filteredUrl . 'sort:price/',
//                    'active' => false,
//                    'reversable' => true,
//                ];
//            }
//            if ($this->nameSortingEnabled) {
//                $this->sortParameters['title'] = [
//                    'url' => $filteredUrl . 'sort:title/',
//                    'active' => false,
//                    'reversable' => true,
//                ];
//            }
//            if ($this->dateSortingEnabled) {
//                $this->sortParameters['date'] = [
//                    'url' => $filteredUrl . 'sort:date/',
//                    'active' => false,
//                    'reversable' => true,
//                ];
//            }
//            $activeSortArgument = $arguments['sort'];
//            $activeOrderArgument = $arguments['order'];
//            if (isset($this->sortParameters[$arguments['sort']])) {
//                $this->sortParameters[$activeSortArgument]['active'] = true;
//                if ($activeSortArgument !== 'manual' && $activeOrderArgument !== 'desc') {
//                    $this->sortParameters[$activeSortArgument]['url'] = $filteredUrl . 'sort:' . $arguments['sort'] . ';' . 'desc/';
//                }
//            }
//        }
//        return $this->sortParameters;
//    }
//
    public function getSortingOptions()
    {
        if ($this->sortingOptions === null) {
            $this->sortingOptions = [];
            $activeSortArgument = $this->getFilterSort();
            $activeOrderArgument = $this->getFilterOrder();
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

    public function getProductsPager()
    {
        if (!$this->productsPager) {
            $this->getProductsList();
        }
        return $this->productsPager;
    }

    protected function getSelectionIdsForFiltering()
    {
        if ($this->selectionsIdsForFiltering === null) {
            $this->selectionsIdsForFiltering = [];
            $conditions = [];
            $collection = persistableCollection::getInstance('module_product_selection');
            if ($records = $collection->conditionalLoad('distinct(id)', $conditions, [], [], [], true)) {
                foreach ($records as &$record) {
                    $this->selectionsIdsForFiltering[] = $record['id'];
                }
            }
        }
        return $this->selectionsIdsForFiltering;
    }

//    protected function getParametersForFiltering()
//    {
//        $selections = [];
//        if ($selectionsIds = $this->getSelectionIdsForFiltering()) {
//            $structureManager = $this->getService('structureManager');
//            $selectionsValuesIndex = $this->getSelectionsValuesIndex($selectionsIds);
//            foreach ($selectionsValuesIndex as $selectionId => &$selectionValuesIds) {
//                $selectionElement = false;
//                $elements = $structureManager->getElementsByIdList((array)$selectionId, $this->id);
//                if ($elements) {
//                    $selectionElement = reset($elements);
//                }
//                if ($selectionElement) {
//                    $selectionValuesIds = array_unique($selectionValuesIds);
//                    $selections[] = [
//                        'selection' => $selectionElement,
//                        'options' => $selectionValuesIds,
//                    ];
//                }
//            }
//        }
//        return $selections;
//    }

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
        $productIdsQuery = $this->getProductsListBaseQuery();
        /**
         * @var Connection $db
         */
        $db = $this->getService('db');
        $query = $db->table('module_product_parameter_value')
            ->whereIn('parameterId', $selectionsIds);
        $query->whereIn('productId', $productIdsQuery);
        $query->select(['parameterId', 'value',])->distinct();
        if ($records = $query->get()) {
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

    /**
     * returns "next" and "previous" products for buttons in selected product
     *
     * @param $productId
     * @return productElement[]
     */
    public function getResidingProducts($productId)
    {
        $result = ['previous' => false, 'next' => false];
        $structureManager = $this->getService('structureManager');
        $query = $this->getProductsListBaseQuery();
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

        return $result;
    }

    public function getDefaultLimit()
    {
        return $this->getService('ConfigManager')->get('main.pageAmountProducts');
    }

    public function getAmountSelectionOptions()
    {
        if ($this->amountSelectionOptions === null) {
            $this->amountSelectionOptions = [];

            if ($this->isAmountSelectionEnabled()) {
                $controller = controller::getInstance();
                $url = $controller->pathURL;
                foreach ($controller->getParameters() as $key => $value) {
                    if (!is_array($value) && $key !== 'page' && $key !== 'limit' && $value !== '') {
                        $url .= $key . ':' . $value . '/';
                    }
                }

                foreach ($this->getService('ConfigManager')->get('main.availablePageAmountProducts') as $limit) {
                    $this->amountSelectionOptions[$limit] = [
                        'url' => $url . 'limit:' . $limit . '/',
                        'selected' => false,
                    ];
                }
                $filterLimit = $this->getFilterLimit();
                if (isset($this->amountSelectionOptions[$filterLimit])) {
                    $this->amountSelectionOptions[$filterLimit]['selected'] = true;
                }
            }
        }
        return $this->amountSelectionOptions;
    }

//    public function getUsedParametersInfo()
//    {
//        if ($this->usedParametersInfo !== null) {
//            return $this->usedParametersInfo;
//        }
//        $this->usedParametersInfo = [];
//        if ($usedParametersIndex = $this->getUsedParametersInfoIndex()) {
//            if ($connectedParameterIds = $this->getParametersIdList()) {
//                $connectedParameterIndex = array_flip($connectedParameterIds);
//                foreach ($usedParametersIndex as $parameterId => $parameterInfo) {
//                    if (isset($connectedParameterIndex[$parameterId])) {
//                        $this->usedParametersInfo[] = $parameterInfo;
//                    }
//                }
//            }
//        }
//        return $this->usedParametersInfo;
//    }

    public function getParametersIdList()
    {
        $linksManager = $this->getService('linksManager');
        return $linksManager->getConnectedIdList($this->id, $this->structureType . 'Parameter', 'parent');
    }

//    protected function getUsedParametersInfoIndex()
//    {
//        if (!is_null($this->usedParametersInfoIndex)) {
//            return $this->usedParametersInfoIndex;
//        }
//        $this->usedParametersInfoIndex = [];
//        if ($products = $this->getProductsList()) {
//            foreach ($products as &$product) {
//                foreach ($product->getPrimaryParametersInfo() as $parameterInfo) {
//                    if (!isset($this->usedParametersInfoIndex[$parameterInfo['id']])) {
//                        $this->usedParametersInfoIndex[$parameterInfo['id']] = $parameterInfo;
//                    }
//                }
//            }
//        }
//        return $this->usedParametersInfoIndex;
//    }

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

    /**
     * Returns the query base for filtering all available product IDs
     *
     * @return Builder
     */
    abstract protected function getProductsListBaseQuery();

    abstract protected function isFilterableByType($filterType);

    abstract public function getConnectedProductsIds();

    abstract public function isAmountSelectionEnabled();

    abstract public function getProductsListCategories();

    public function getProductsListBrands()
    {
        $result = [];
        $productIdsQuery = clone $this->getFilteredProductsQuery();
        if ($records = $productIdsQuery
            ->select('brandId')->distinct()
            ->where('brandId', '!=', 0)
            ->get()) {
            $structureManager = $this->getService('structureManager');
            foreach ($records as $record) {
                if ($brandElement = $structureManager->getElementById($record['brandId'])) {
                    $result[] = $brandElement;
                }
            }
        }
        return $result;
    }

//    public function getProductsListCategories()
//    {
//        $productsListCategories = [];
//        $productIdsQuery = $this->getProductsListBaseQuery();
//        /**
//         * @var \Illuminate\Database\Connection $db
//         */
//        $db = $this->getService('db');
//        $query = $db->table('structure_links')
//            ->where('type', '=', 'catalogue');
//        $query->whereIn('childStructureId', $productIdsQuery);
//        $query->select(['parentStructureId',])->distinct();
//        if ($records = $query->get()) {
//            $ids = array_column($records, 'childStructureId');
//        }
//        return $ids;


//        $topLevelCategoriesIds = [];
//        $structureManager = $this->getService('structureManager');
//        $categoriesElementId = $structureManager->getElementIdByMarker('categories');
//        $categoriesIds = $this->getService('linksManager')
//            ->getConnectedIdList($categoriesElementId, 'structure', 'parent');
//        if ($categoriesIds) {
//            $currentLanguageId = $this->getService('languagesManager')->getCurrentLanguageId();
//            foreach ($categoriesIds as &$categoryId) {
//                if ($structureManager->getElementById($categoryId, $currentLanguageId)) {
//                    $topLevelCategoriesIds[] = $categoryId;
//                }
//            }
//        }
//        return $topLevelCategoriesIds;
//    }
}