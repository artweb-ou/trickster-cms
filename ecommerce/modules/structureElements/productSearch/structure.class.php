<?php
//
//class productSearchElement extends menuDependantStructureElement
//{
//    use ProductFilterFactoryTrait;
//    public $dataResourceName = 'module_productsearch';
//    public $defaultActionName = 'show';
//    protected $allowedTypes = [];
//    public $role = 'content';
//    protected $connectedParameters;
//    protected $searchCutLength = 200;
//    protected $pager;
//    protected $priceRangeOptions;
//    protected $catalogue;
//    protected $brands;
//    protected $discounts;
//    protected $brandFilter;
//    protected $discountFilter;
//    protected $filtersIndex;
//    protected $baseFilter;
//    protected $filtersPrepared = false;
//
//    protected function setModuleStructure(&$moduleStructure)
//    {
//        $moduleStructure['title'] = 'text';
//        $moduleStructure['filterCategory'] = 'checkbox';
//        $moduleStructure['filterBrand'] = 'checkbox';
//        $moduleStructure['filterPrice'] = 'checkbox';
//        $moduleStructure['filterDiscount'] = 'checkbox';
//        $moduleStructure['availabilityFilterEnabled'] = 'checkbox';
//        $moduleStructure['sortingEnabled'] = 'checkbox';
//        $moduleStructure['priceInterval'] = 'naturalNumber';
//
//        $moduleStructure['pageDependent'] = 'checkbox';
//        $moduleStructure['checkboxesForParameters'] = 'checkbox';
//        $moduleStructure['pricePresets'] = 'checkbox';
//        // temporary
//        $moduleStructure['parametersIds'] = 'numbersArray';
//        $moduleStructure['categoryId'] = 'text';
//        $moduleStructure['brandId'] = 'text';
//        $moduleStructure['selectionValues'] = 'array';
//        $moduleStructure['parameterValues'] = 'array';
//        $moduleStructure['catalogueFilterId'] = 'text';
//    }
//
//    public function isApplied()
//    {
//        $filterParameters = [
//            'productsearch',
//            'category',
//            'brand',
//            'discount',
//            'parameter',
//            'price',
//        ];
//        $controller = controller::getInstance();
//        foreach ($filterParameters as &$filterParameter) {
//            if ($controller->getParameter($filterParameter)) {
//                return true;
//            }
//        }
//        return false;
//    }
//
//    public function addFilter(productFilter $filter)
//    {
//        if ($this->baseFilter !== null) {
//            $this->baseFilter->addFilter($filter);
//        } else {
//            $this->baseFilter = $filter;
//        }
//        $this->registerFilter($filter);
//    }
//
//    protected function registerFilter(productFilter $filter)
//    {
//        $type = $filter->getType();
//        if (!isset($this->filtersIndex[$type])) {
//            $this->filtersIndex[$type] = [];
//        }
//        $this->filtersIndex[$type][] = $filter;
//    }
//
//    public function getCurrentElement()
//    {
//        return $this->getService('structureManager')->getCurrentElement(controller::getInstance()->requestedPath);
//    }
//
//    public function getSearchBaseElement()
//    {
//        $searchBaseElement = false;
//        if ($this->pageDependent) {
//            $currentElement = $this->getCurrentElement();
//            if ($currentElement) {
//                if ($currentElement instanceof productElement) {
//                    $searchBaseElement = $this->getLastVisitedCategory();
//                } elseif ($currentElement instanceof productsListStructureElement && $currentElement->structureType != 'productCatalogue'
//                ) {
//                    $searchBaseElement = $currentElement;
//                }
//            }
//        }
//        if (!$searchBaseElement) {
//            $searchBaseElement = $this->getCatalogue();
//        }
//        return $searchBaseElement;
//    }
//
//    public function getArguments()
//    {
//        $arguments = [];
//        if ($sourceElement = $this->getSearchBaseElement()) {
//            $arguments = $sourceElement->getFilterArguments();
//        }
//        return $arguments;
//    }
//
//    public function canActLikeFilter()
//    {
//        $currentElement = $this->getCurrentElement();
//        return ($this->pageDependent && (($currentElement && $currentElement instanceof productsListStructureElement && $currentElement->structureType != 'productCatalogue') || ($currentElement instanceof productElement && $this->getLastVisitedCategory())));
//    }
//
//    public function getLastVisitedCategory()
//    {
//        $result = null;
//        $categoryId = $this->getService('user')->getStorageAttribute('lastCategoryId');
//        if ($categoryId) {
//            $result = $this->getService('structureManager')->getElementById($categoryId);
//        }
//        return $result;
//    }
//
//    public function getCachedArguments()
//    {
//        $result = [];
//        if ($data = $this->getService('user')->getStorageAttribute('lastSearchArguments')) {
//            $result = json_decode($data, true);
//        }
//        return $result;
//    }
//
//    public function getFilters()
//    {
//        if (!$this->filtersPrepared) {
//            $this->filtersPrepared = true;
//            if ($this->pageDependent) {
//                $currentElement = $this->getCurrentElement();
//                $sourceElement = $this->getSearchBaseElement();
//
//                if ($sourceElement) {
//                    $gotFilters = false;
//                    if ($currentElement instanceof productElement) {
//                        $arguments = $this->getCachedArguments();
//                        if ($arguments) {
////                            $sourceElement->getFilteredProductIds();
//                            $gotFilters = true;
//                        }
//                    }
//                    if (!$gotFilters) {
//                        $arguments = $sourceElement->getFilterArguments();
//                        if (!$sourceElement->requested) {
////                            $sourceElement->getFilteredProductIds();
//                        } else {
//                            $sourceElement->getProductsList();
//                        }
//                    }
//                    if ($pageFilters = $sourceElement->getFiltersIndex()) {
//                        $this->filtersIndex = $pageFilters;
//                        if (!$this->filterCategory) {
//                            unset($this->filtersIndex['category']);
//                        }
//                        if (!$this->filterBrand) {
//                            unset($this->filtersIndex['brand']);
//                        }
//                        if (!$this->filterDiscount) {
//                            unset($this->filtersIndex['discount']);
//                        }
//                        if (!$this->availabilityFilterEnabled) {
//                            unset($this->filtersIndex['availability']);
//                        }
//                        if (!$this->filterPrice) {
//                            unset($this->filtersIndex['price']);
//                        }
//                    }
//                }
//
//                $user = $this->getService('user');
//                if ($currentElement && $currentElement instanceof categoryElement) {
//                    $user->setStorageAttribute('lastCategoryId', $currentElement->id);
//                    $user->setStorageAttribute('lastSearchArguments', json_encode($arguments));
//                } elseif (!($currentElement instanceof productElement)) {
//                    $user->setStorageAttribute('lastCategoryId', '');
//                    $user->setStorageAttribute('lastSearchArguments', '');
//                }
//                if (!($currentElement instanceof productsListStructureElement || $currentElement instanceof productElement)) {
//                    unset($this->filtersIndex['parameter']);
//                }
//            } elseif ($sourceElement = $this->getCatalogue()) {
//                $arguments = $sourceElement->parseSearchArguments();
//                $availableProductsIds = $sourceElement->getProductsListBaseIds();
//                if ($this->filterCategory && $sourceElement->categorized) {
//                    $currentElement = $this->getCurrentElement();
//                    if ($currentElement && $currentElement->structureType == 'category') {
//                        foreach ($filters = $currentElement->getTreeFilters() as $filter) {
//                            $this->addFilter($filter);
//                        }
//                    } elseif ($categoriesIds = $this->getTopLevelCategoriesIds()) {
//                        $this->addFilter($this->createProductFilter('category', $arguments['category'], $categoriesIds));
//                    }
//                }
//                if ($this->filterBrand) {
//                    $this->addFilter($this->createProductFilter('brand', $arguments['brand']));
//                }
//                if ($this->filterDiscount) {
//                    $this->addFilter($this->createProductFilter('discount', $arguments['discount']));
//                }
//                if ($this->availabilityFilterEnabled) {
//                    $this->addFilter(new availabilityProductFilter($arguments['availability']));
//                }
//                if ($parameters = $this->getConnectedParameters()) {
//                    foreach ($parameters as &$parameter) {
//                        if ($parameter->structureType == 'productSelection') {
//                            $optionsIds = [];
//                            foreach ($parameter->getSelectionOptions() as $option) {
//                                $optionsIds[] = $option->id;
//                            }
//                            if ($optionsIds) {
//                                $filter = $this->createProductFilter('parameter', array_intersect($arguments['parameter'], $optionsIds));
//                                $filter->setSelectionId($parameter->id);
//                                $this->addFilter($filter);
//                            }
//                        }
//                    }
//                }
//                if ($this->filterPrice) {
//                    $filter = $this->createProductFilter('price', $arguments['price']);
//                    $filter->setRangeInterval($this->priceInterval);
//                    $this->addFilter($filter);
//                }
//                if ($this->baseFilter !== null) {
//                    $this->baseFilter->apply($availableProductsIds);
//                }
//            }
//        }
//    }
//
//    public function getSortParameters()
//    {
//        $translationsManager = $this->getService('translationsManager');
//        return [
//            [
//                'title' => $translationsManager->getTranslationByName('productsearch.order_unspecified'),
//                'value' => '',
//            ],
//            [
//                'title' => $translationsManager->getTranslationByName('productsearch.order_price'),
//                'value' => 'price;asc',
//            ],
//            [
//                'title' => $translationsManager->getTranslationByName('productsearch.order_price_desc'),
//                'value' => 'price;desc',
//            ],
//            [
//                'title' => $translationsManager->getTranslationByName('productsearch.order_title'),
//                'value' => 'title;asc',
//            ],
//            [
//                'title' => $translationsManager->getTranslationByName('productsearch.order_title_desc'),
//                'value' => 'title;desc',
//            ],
//        ];
//    }
//
//    public function getFiltersByType($type)
//    {
//        $this->getFilters();
//        return isset($this->filtersIndex[$type]) ? $this->filtersIndex[$type] : [];
//    }
//
//    public function getCatalogue()
//    {
//        if ($this->catalogue === null) {
//            $this->catalogue = false;
//            if ($connectedCataloguesIds = $this->getService('linksManager')
//                ->getConnectedIdList($this->id, 'productSearchCatalogue', 'parent')
//            ) {
//                $this->catalogue = $this->getService('structureManager')->getElementById($connectedCataloguesIds[0]);
//            }
//        }
//        return $this->catalogue;
//    }
//
//    public function getConnectedParametersIds()
//    {
//        return $this->getService('linksManager')->getConnectedIdList($this->id, "productSearchParameter", 'parent');
//    }
//
//    public function getConnectedParameters()
//    {
//        if ($this->connectedParameters === null) {
//            $this->connectedParameters = [];
//            if ($connectedParametersIds = $this->getConnectedParametersIds()) {
//                $parameters = $this->getService('structureManager')
//                    ->getElementsByIdList($connectedParametersIds, $this->id);
//                foreach ($parameters as $parameter) {
//                    $item = [];
//                    $item['id'] = $parameter->id;
//                    $item['title'] = $parameter->getTitle();
//                    $item['select'] = true;
//                    $this->connectedParameters[] = $item;
//                }
//            }
//        }
//        return $this->connectedParameters;
//    }
//
//    public function getCategoriesInfo()
//    {
//        $categoriesInfo = [];
//        $structureManager = $this->getService('structureManager');
//        $categories = $structureManager->getElementsByType('category', $this->getService('languagesManager')
//            ->getCurrentLanguageId());
//        if ($categories) {
//            $mainCategoriesInfo = [];
//            $subCategoriesInfo = [];
//
//            foreach ($categories as &$category) {
//                $parentElement = $structureManager->getElementsFirstParent($category->id);
//                if ($parentElement->structureType === 'folder') {
//                    $mainCategoriesInfo[] = [
//                        'title' => $category->title,
//                        'id' => $category->id,
//                    ];
//                } elseif ($parentElement->structureType === 'category') {
//                    $parentElementId = $parentElement->id;
//                    if (!isset($subCategoriesInfo[$parentElementId])) {
//                        $subCategoriesInfo[$parentElementId] = [];
//                    }
//                    $subCategoriesInfo[$parentElementId][] = [
//                        'id' => $category->id,
//                        'title' => $category->title,
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
//
//    public function getPriceRangeOptions()
//    {
//        if (null === $this->priceRangeOptions) {
//            $this->priceRangeOptions = [];
//            if ($this->priceInterval) {
//                $collection = persistableCollection::getInstance('module_product');
//                $conditions = [
//                    [
//                        'availability',
//                        '!=',
//                        'unavailable',
//                    ],
//                ];
//
//                if ($records = $collection->conditionalLoad('distinct(price)', $conditions, ['price' => 'asc'], [], [], true)
//                ) {
//                    $prices = [];
//                    foreach ($records as &$record) {
//                        $prices[] = ceil($record['price']);
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
//
//    public function getDiscounts()
//    {
//        if ($this->discounts === null) {
//            $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
//            $this->discounts = $shoppingBasketDiscounts->getApplicableDiscountsList();
//        }
//        return $this->discounts;
//    }
//
//    public function performSearch(array $arguments)
//    {
//        $results = [];
//        $structureManager = $this->getService('structureManager');
//        $productsIds = [];
//        if ($arguments['categoryId']) {
//            $categoryElement = $structureManager->getElementById($arguments['categoryId']);
//            if ($categoryElement && $categoryElement->structureType === 'category') {
//                $productsIds = $categoryElement->getConnectedProductsIds();
//            }
//        } else {
//            $collection = persistableCollection::getInstance('module_product');
//            if ($records = $collection->conditionalLoad('distinct(id)', [
//                'id',
//                '<>',
//                '0',
//            ], [], [], [], true)
//            ) {
//                foreach ($records as &$record) {
//                    $productsIds[] = $record['id'];
//                }
//            }
//        }
//        $productsIds = array_unique($productsIds);
//        if ($productsIds) {
//            if ($productsIds && $arguments['brandId']) {
//                $this->filterProductsByBrand($arguments['brandId'], $productsIds);
//            }
//            if ($productsIds && $arguments['parametersValues']) {
//                $this->filterProductsByParameterValues($arguments['parametersValues'], $productsIds);
//            }
//
//            // Query the products...
//            if ($productsIds) {
//                $collection = persistableCollection::getInstance('module_product');
//
//                // filter out products that are out of stock or hidden
//                $conditions = [
//                    [
//                        'availability',
//                        '=',
//                        'quantity_dependent',
//                    ],
//                    [
//                        'quantity',
//                        '=',
//                        '0',
//                    ],
//                ];
//                if ($records = $collection->conditionalLoad('id', $conditions)) {
//                    $unavailableProductsIds = [];
//                    foreach ($records as &$record) {
//                        $unavailableProductsIds[] = $record['id'];
//                    }
//                    $productsIds = array_diff($productsIds, $unavailableProductsIds);
//                }
//
//                if ($productsIds) {
//                    $conditions = [
//                        [
//                            'inactive',
//                            '!=',
//                            '1',
//                        ],
//                        [
//                            'availability',
//                            '!=',
//                            'unavailable',
//                        ],
//                        [
//                            'id',
//                            'in',
//                            $productsIds,
//                        ],
//                        [
//                            'languageId',
//                            '=',
//                            $this->getService('languagesManager')->getCurrentLanguageId(),
//                        ],
//                    ];
//
//                    if ($arguments['price']) {
//                        $priceRange = explode('-', $arguments['price']);
//                        if (count($priceRange) === 2) {
//                            $conditions[] = [
//                                'price',
//                                '>=',
//                                $priceRange[0],
//                            ];
//                        }
//                        $conditions[] = [
//                            'price',
//                            '<=',
//                            $priceRange[1],
//                        ];
//                    }
//
//                    if ($elementsCount = $collection->countElements('id', $conditions)) {
//                        $pagerURL = $this->generatePagerUrl();
//
//                        $elementsOnPage = 30;
//                        $page = (int)controller::getInstance()->getParameter('page');
//
//                        $pager = new pager($pagerURL, $elementsCount, $elementsOnPage, $page, 'page');
//                        $this->pager = $pager;
//
//                        // Query the relevant product IDs
//                        $limitFields = [
//                            $pager->startElement,
//                            $elementsOnPage,
//                        ];
//
//                        $order = explode(';', $arguments['order']);
//
//                        //complete the filtration to exclude inactive or unavailable products
//                        $productsIds = [];
//                        if ($records = $collection->conditionalLoad('id', $conditions, [$order[0] => $order[1]], $limitFields)
//                        ) {
//                            foreach ($records as &$record) {
//                                $productsIds[] = $record['id'];
//                            }
//                        }
//
//                        // Create the the product objects list
//                        if (count($productsIds) > 0) {
//                            foreach ($productsIds as &$productId) {
//                                if ($product = $structureManager->getElementById($productId)) {
//                                    $results[] = $product;
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        return $results;
//    }
//
//    protected function filterProductsByBrand($brandId, array &$productsIds)
//    {
//        $collection = persistableCollection::getInstance('structure_links');
//        $conditions = [
//            [
//                'childStructureId',
//                'IN',
//                $productsIds,
//            ],
//            [
//                'parentStructureId',
//                '=',
//                $brandId,
//            ],
//            [
//                'type',
//                '=',
//                "productbrand",
//            ],
//        ];
//        $productsIds = [];
//        if ($records = $collection->conditionalLoad('childStructureId', $conditions)) {
//            foreach ($records as &$record) {
//                $productsIds[] = $record['childStructureId'];
//            }
//        }
//    }
//
//    protected function filterProductsByParameterValues(array $parametersValues, array &$productsIds)
//    {
//        $collection = persistableCollection::getInstance('module_product_parameter_value');
//        foreach ($parametersValues as $key => &$value) {
//            $conditions = [
//                [
//                    'parameterId',
//                    '=',
//                    $key,
//                ],
//                [
//                    'value',
//                    '=',
//                    $value,
//                ],
//                [
//                    'productId',
//                    'IN',
//                    $productsIds,
//                ],
//            ];
//            $productsIds = [];
//            if ($records = $collection->conditionalLoad('productId', $conditions)) {
//                foreach ($records as &$record) {
//                    $productsIds[] = $record['productId'];
//                }
//            } else {
//                break;
//            }
//        }
//    }
//
//    protected function generatePagerUrl()
//    {
//        $url = $this->URL . 'id:' . $this->id . '/action:perform/';
//        $parametersValues = $searchArguments['parametersValues'];
//        $filters = [];
//        foreach ($parametersValues as $key => &$value) {
//            $filters[] = $key . ',' . $value;
//        }
//        if ($filters) {
//            $url .= 'filters:' . implode(';', $filters) . '/';
//        }
//        if ($searchArguments['category']) {
//            $url .= 'category:' . $searchArguments['category'] . '/';
//        }
//        if ($searchArguments['brand']) {
//            $url .= 'brand:' . $searchArguments['category'] . '/';
//        }
//        if ($searchArguments['discount']) {
//            $url .= 'discount:' . $searchArguments['discount'] . '/';
//        }
//        if ($searchArguments['price']) {
//            $url .= 'price:' . $searchArguments['price'] . '/';
//        }
//        $url .= 'sort:' . $searchArguments['order'] . '/';
//        return $url;
//    }
//    public function getPager()
//    {
//        return $this->pager;
//    }
//
//    public function canBeDisplayed()
//    {
//        $this->getFilters();
//        if (!$this->getSearchBaseElement()) {
//            return false;
//        }
//        if (controller::getInstance()->getParameter('productSearch')) {
//            return true;
//        }
//        if (($this->canActLikeFilter() || !$this->pageDependent) && $this->sortingEnabled) {
//            return true;
//        }
//        $filterTypes = [
//            'category',
//            'parameter',
//            'discount',
//            'brand',
//        ];
//        if ($this->canActLikeFilter() || !$this->pageDependent) {
//            $filterTypes[] = 'price';
//        }
//        foreach ($filterTypes as $type) {
//            foreach ($this->getFiltersByType($type) as $filter) {
//                if ($filter->isRelevant()) {
//                    return true;
//                }
//            }
//        }
//        return false;
//    }
//}
//
//
