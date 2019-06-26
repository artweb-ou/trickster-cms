<?php

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

/**
 * Class productsListElement
 *
 * @property int $priceSortingEnabled
 * @property int $nameSortingEnabled
 * @property int $dateSortingEnabled
 * @property int $brandSortingEnabled
 *
 */
abstract class ProductsListElement extends menuStructureElement
{
    use ProductFilterFactoryTrait;
    use CacheOperatingElement;

    protected $productsList;

    protected $productsListBaseQuery;
    protected $productsListBaseAmount;

    protected $filteredProductsQuery;
    protected $filteredProductsAmount;
    /**
     * @var pager
     */
    protected $productsPager;
    protected $amountSelectionOptions;
    protected $filterArguments;
    protected $priceRangeSets;
    protected $selectionsIdsForFiltering;
    protected $selectionsValuesIndex;
    protected $sortingOptions;
    protected $usedParametersInfo;
    protected $usedParametersInfoIndex;

    protected $filterCategoryIds;
    protected $filterBrandIds;
    protected $filterDiscountIds;
    protected $filterParameterValueIds;
    protected $filterParameterValues;
    protected $filterAvailability;
    protected $filterSort;
    protected $filterOrder;
    protected $filterPrice;
    protected $filterPriceString;
    protected $filterLimit;
    protected $priceInterval = 5;
    protected $cacheKey;

    public function getProductsListElement()
    {
        return $this;
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
        $languagesManager = $this->getService('languagesManager');

        //basic query to get all non-hidden products available in stock
        $query = $db->table('module_product');
        $query->select(['module_product.id', 'module_product.title', 'module_product.brandId', 'module_product.availability', 'module_product.price']);
        $query->where('module_product.languageId', '=', $languagesManager->getCurrentLanguageId());
        $query->where('inactive', '!=', '1');
        $query->where(function (Builder $query) {
            $query->orWhere('availability', '=', 'available');
            $query->orWhere('availability', '=', 'inquirable');
            $query->orWhere('availability', '=', 'available_inquirable');
            $query->orWhere(function (Builder $query) {
                $query->where('availability', '=', 'quantity_dependent');
                $query->where('quantity', '!=', 0);
            });
        });
        //required for any kinds of joins made with this query outside of this method, prevents duplicated product rows from being selected
        $query->groupBy('module_product.id');

        return $query;
    }

    protected $productsListBaseOptimizedQuery;

    protected function getProductsListBaseOptimizedQuery()
    {
        if ($this->productsListBaseOptimizedQuery === null) {
            /**
             * @var Connection $db
             */
            $db = $this->getService('db');

            $productsListBaseQuery = $this->getProductsListBaseQuery();
            $db->insert($db->raw("DROP TABLE IF EXISTS engine_baseproducts"));
            $db->insert($db->raw("CREATE TEMPORARY TABLE engine_baseproducts " . $productsListBaseQuery->toSql()), $productsListBaseQuery->getBindings());
            $this->productsListBaseOptimizedQuery = $db->table('baseproducts')->select('id');
        }
        return $this->productsListBaseOptimizedQuery;
    }

    protected function getFilteredProductsQuery()
    {
        if ($this->filteredProductsQuery === null) {
            $this->filteredProductsQuery = false;

            $filteredProductsQuery = clone $this->getProductsListBaseOptimizedQuery();
            /**
             * @var structureManager $structureManager
             */
            $structureManager = $this->getService('structureManager');
            if ($categoryIds = $this->getFilterCategoryIds()) {
                $deepCategoryIds = [];

                foreach ($categoryIds as $categoryId) {
                    /**
                     * @var categoryElement $category
                     */
                    if ($category = $structureManager->getElementById($categoryId)) {
                        $category->gatherSubCategoriesIdIndex($categoryId, $deepCategoryIds);
                    }
                }
                if ($deepCategoryIds) {
                    $deepCategoryIds = array_keys($deepCategoryIds);
                    $filteredProductsQuery->whereIn('baseproducts.id', function ($query) use ($deepCategoryIds) {
                        $query->from('structure_links')
                            ->select('childStructureId')
                            ->whereIn('parentStructureId', $deepCategoryIds)
                            ->where('type', '=', 'catalogue');
                    });
                }
            }
            if ($brandsIds = $this->getFilterBrandIds()) {
                $filteredProductsQuery->whereIn('brandId', $brandsIds);
            }
            if ($discountIds = $this->getFilterDiscountIds()) {
                /**
                 * @var shoppingBasketDiscounts $shoppingBasketDiscounts
                 */
                $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
                $discountedProductIds = [];
                foreach ($discountIds as $discountId) {
                    if ($discount = $shoppingBasketDiscounts->getDiscount($discountId)) {
                        $discountedProductIds = array_merge($discountedProductIds, $discount->getApplicableProductsIds());
                    }
                }
                $filteredProductsQuery->whereIn('baseproducts.id', $discountedProductIds);
            }
            if ($availability = $this->getFilterAvailability()) {
                $statuses = [];
                if (in_array('available', $availability)) {
                    $statuses[] = 'available';
                    $statuses[] = 'quantity_dependent';
                    $statuses[] = 'available_inquirable';
                }
                if (in_array('inquirable', $availability)) {
                    $statuses[] = 'inquirable';
                }
                $filteredProductsQuery->whereIn('availability', $statuses);
            }
            if ($parameterValues = $this->getFilterParameterValueIds()) {
                //we can't send a single request here because it drops down database when we have >10 parameters in search
                //instead we use php-based intersection
                //todo: change logic to work with temporary table to avoid transferring IDs
                $ids = null;
                /**
                 * @var Connection $db
                 */
                $db = $this->getService('db');
                foreach ($parameterValues as $counter => $parameterValue) {
                    $query = $db->table('module_product_parameter_value')
                        ->select('productId')
                        ->where('value', '=', $parameterValue);
                    if ($ids !== null) {
                        $query->whereIn('productId', $ids);
                    }
                    $records = $query->get();
                    $ids = array_column($records, 'productId');
                    if (!$ids) {
                        break;
                    }
                }
                $filteredProductsQuery->whereIn('baseproducts.id', $ids);
            }

            if ($price = $this->getFilterPrice()) {
                $filteredProductsQuery->where('baseproducts.price', '>=', $price[0]);
                $filteredProductsQuery->where('baseproducts.price', '<=', $price[1]);
            }
            $filteredProductsQuery->select('*');
            /**
             * @var Connection $db
             */
            $db = $this->getService('db');
            $db->insert($db->raw("DROP TABLE IF EXISTS engine_filteredproducts"));
            $db->insert($db->raw("CREATE TEMPORARY TABLE engine_filteredproducts " . $filteredProductsQuery->toSql()), $filteredProductsQuery->getBindings());
            $this->filteredProductsQuery = $db->table('filteredproducts')->select('filteredproducts.id');
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
        $cacheKey = $this->getCacheKey();
        $cache = $this->getElementsListCache('prList:' . $cacheKey, 3600);
        if ($this->productsList = $cache->load()) {
            return $this->productsList;
        }

        $this->productsList = [];
        if ($filteredProductsQuery = clone $this->getFilteredProductsQuery()) {
            $sort = $this->getFilterSort();
            $order = $this->getFilterOrder();
            if ($sort && $sort == 'manual') {
                $this->applyManualSorting($filteredProductsQuery, $filteredProductsQuery);
            } elseif ($sort == 'date') {
                $filteredProductsQuery->join('structure_elements', 'filteredproducts.id', '=', 'structure_elements.id', 'left');
                $filteredProductsQuery->orderBy('structure_elements.dateCreated', $order);
            } elseif ($sort == 'brand') {
                $filteredProductsQuery->join('module_brand', 'filteredproducts.brandId', '=', 'module_brand.id', 'left');
                $filteredProductsQuery->orderBy('module_brand.title', $order);
            } else {
                $filteredProductsQuery->orderBy($sort, $order);
            }

            $pager = $this->getProductsPager();

            $filteredProductsQuery->skip($pager->startElement)->take($this->getFilterLimit())->groupBy('id');
            if ($records = $filteredProductsQuery->get()) {
                $productIds = array_column($records, 'id');
                $parentRestrictionId = $this->getProductsListParentRestrictionId();
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                foreach ($productIds as &$productId) {
                    if ($product = $structureManager->getElementById($productId, $parentRestrictionId)) {
                        $this->productsList[] = $product;
                    }
                }
            }
        }
        $cache->save($this->productsList);

        return $this->productsList;
    }

    public function getFilteredProductsAmount()
    {
        if ($this->filteredProductsAmount === null) {
            /**
             * @var Cache $cache
             */
            $cache = $this->getService('Cache');
            $key = $this->getProductsListElement()->getCacheKey();
            if (($this->filteredProductsAmount = $cache->get($this->id . ':famount:' . $key)) === false) {
                if ($query = $this->getFilteredProductsQuery()) {
                    $this->filteredProductsAmount = $query->count('filteredproducts.id');
                    $cache->set($this->id . ':famount:' . $key, $this->filteredProductsAmount);
                }
            }
        }
        return $this->filteredProductsAmount;
    }

    public function getProductsListBaseAmount()
    {
        if ($this->productsListBaseAmount === null) {
            /**
             * @var Cache $cache
             */
            $cache = $this->getService('Cache');
            $key = $this->getProductsListElement()->getCacheKey();
            if (($this->productsListBaseAmount = $cache->get($this->id . ':bamount:' . $key)) === false) {
                if ($query = $this->getProductsListBaseOptimizedQuery()) {
                    $this->productsListBaseAmount = $query->count('id');
                    $cache->set($this->id . ':bamount:' . $key, $this->productsListBaseAmount);
                }
            }
        }
        return $this->productsListBaseAmount;
    }

    /**
     * @param Builder $query
     * @param Builder $productsIds
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
            $query->orderByRaw($query->raw("FIELD(id, " . implode(',', $sortIds)) . ")");
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
    public function getFilterParameterValueIds()
    {
        if ($this->filterParameterValueIds === null) {
            $this->filterParameterValueIds = $this->getArgumentForFilter('parameter');
        }
        return $this->filterParameterValueIds;
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
            if (($priceString = $this->getFilterPriceString()) && strpos($priceString, '-') !== false) {
                $this->filterPrice = explode('-', $priceString);
            }
        }
        return $this->filterPrice;
    }

    /**
     * @return string
     */
    public function getFilterPriceString()
    {
        if ($this->filterPriceString === null) {
            $controller = controller::getInstance();
            $this->filterPriceString = $controller->getParameter('price');
        }
        return $this->filterPriceString;
    }

    public function getSelectedFiltersCount()
    {
        $selectedFiltersCount = count($this->getFilterParameterValueIds());
        if ($this->getFilterPrice()) {
            $selectedFiltersCount++;
        }
        return $selectedFiltersCount;
    }

    /**
     * @return mixed
     */
    public function getFilterLimit()
    {
        if ($this->filterLimit === null) {
            if ($limit = $this->getProductsListFixedLimit()) {
                $this->filterLimit = $limit;
            } else {
                $controller = controller::getInstance();
                $limitArgument = $controller->getParameter('limit');

                if ((int)$limitArgument) {
                    $this->filterLimit = (int)$limitArgument;
                } else {
                    $this->filterLimit = $this->getDefaultLimit();
                }
            }
        }
        return $this->filterLimit;
    }

    //some modules dont use pages or dynamic amount, they use strict limit of displayed products instead
    protected function getProductsListFixedLimit()
    {
        return false;
    }

    /**
     * @deprecated
     */
    public function getFilterArguments()
    {
        return $this->filterArguments;
    }

    public function getProductsListPriceRangeSets()
    {
        if ($this->priceRangeSets === null) {
            $this->priceRangeSets = [];
            if ($this->priceInterval) {
                // we cannot use price range according to filtered list, because we would always get different ranges
                $query = clone $this->getProductsListBaseOptimizedQuery();
                $query->select(['price'])->distinct()
                    ->orderBy('price', 'asc');
                if ($records = $query->get()) {
                    $distinctPrices = [];
                    foreach ($records as &$record) {
                        $distinctPrices[] = $record['price'];
                    }

                    $this->productsListMinPrice = reset($distinctPrices);
                    $this->productsListMaxPrice = end($distinctPrices);

                    $priceCount = count($distinctPrices);
                    $priceChunks = array_chunk($distinctPrices, max(ceil($priceCount / $this->priceInterval), 2));
                    foreach ($priceChunks as $priceChunk) {
                        $this->priceRangeSets[] = [
                            $priceChunk[0],
                            array_pop($priceChunk),
                        ];
                    }
                }
            }
        }
        return $this->priceRangeSets;
    }

    public function isFilterable()
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

    public function isFieldSortable($field)
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
            $filteredProductsAmount = $this->getFilteredProductsAmount();
            //sometimes we dont need all the products, but only amount set by admin
            if (($fixedLimit = $this->getProductsListFixedLimit()) && $fixedLimit < $filteredProductsAmount) {
                $filteredProductsAmount = $fixedLimit;
            }
            $this->productsPager = new pager($this->generatePagerUrl(), $filteredProductsAmount, $this->getFilterLimit(), (int)controller::getInstance()
                ->getParameter('page'), "page");
        }
        return $this->productsPager;
    }

    protected function getSelectionIdsForFiltering()
    {
        if ($this->selectionsIdsForFiltering === null) {
            $this->selectionsIdsForFiltering = [];
            /**
             * @var Connection $db
             */
            $db = $this->getService('db');
            $query = $db->table('module_product_selection')
                ->select('id')->distinct()
                ->where('filter', '=', 1);

            if ($records = $query->get()) {
                $this->selectionsIdsForFiltering = array_column($records, 'id');
            }
        }
        return $this->selectionsIdsForFiltering;
    }

    public function getParameterSelectionsForFiltering()
    {
        $parameters = [];
        if ($selectionsIds = $this->getSelectionIdsForFiltering()) {
            /**
             * @var structureManager $structureManager
             */
            $structureManager = $this->getService('structureManager');
            foreach ($selectionsIds as $selectionId) {
                if ($selectionElement = $structureManager->getElementById($selectionId)) {
                    $parameters[] = $selectionElement;
                }
            }
        }
        return $parameters;
    }

    public function getProductsListSelectionValues($selectionId)
    {
        $valueElements = [];
        if ($selectionsValuesIndex = $this->getSelectionsValuesIndex()) {
            if (isset($selectionsValuesIndex[$selectionId])) {
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                $sort = [];
                foreach ($selectionsValuesIndex[$selectionId] as $selectionValuesId) {
                    if ($valueElement = $structureManager->getElementById($selectionValuesId)) {
                        $valueElements[] = $valueElement;
                        $sort[] = $valueElement->getTitle();
                    }
                }
                array_multisort($sort, SORT_ASC, $valueElements);
            }
        }

        return $valueElements;
    }

    protected function getSelectionsValuesIndex()
    {
        if ($this->selectionsValuesIndex === null) {
            $this->selectionsValuesIndex = [];

            if ($selectionsIds = $this->getSelectionIdsForFiltering()) {
                $productIdsQuery = clone $this->getFilteredProductsQuery();
                /**
                 * @var Connection $db
                 */
                $db = $this->getService('db');
                $query = $db->table('module_product_parameter_value')
                    ->whereIn('parameterId', $selectionsIds);
                $query->whereIn('productId', $productIdsQuery);
                $query->select(['parameterId', 'value'])->distinct();
                if ($records = $query->get()) {
                    foreach ($records as &$record) {
                        if (!isset($this->selectionsValuesIndex[$record['parameterId']])) {
                            $this->selectionsValuesIndex[$record['parameterId']] = [];
                        }
                        $this->selectionsValuesIndex[$record['parameterId']][] = $record['value'];
                    }
                }
            }
        }
        return $this->selectionsValuesIndex;
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
        $query = $this->getProductsListBaseOptimizedQuery();
        $productsIds = [];
        if ($records = $query->get('id')) {
            $productsIds = array_column($records, 'id');
        }
        if ($productsIds) {
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

    /**
     * @return mixed
     */
    public function getParametersIdList()
    {
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');
        return $linksManager->getConnectedIdList($this->id, $this->structureType . 'Parameter', 'parent');
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

    /**
     * Returns the query base for filtering all available product IDs
     *
     * @return Builder
     */
    abstract protected function getProductsListBaseQuery();

    abstract public function isAmountSelectionEnabled();

    public function getProductsListAvailabilityTypes()
    {
        $result = [];
        $productIdsQuery = clone $this->getFilteredProductsQuery();
        if ($records = $productIdsQuery
            ->select('filteredproducts.availability')->distinct()
            ->get()) {
            foreach ($records as $record) {
                $result[] = $record['availability'];
            }
        }
        return $result;
    }

    public function getProductsListBrands()
    {
        $result = [];
        $productIdsQuery = clone $this->getFilteredProductsQuery();
        if ($records = $productIdsQuery
            ->select('filteredproducts.brandId')->distinct()
            ->where('filteredproducts.brandId', '!=', 0)
            ->get()) {
            $structureManager = $this->getService('structureManager');
            $sort = [];
            foreach ($records as $record) {
                if ($brandElement = $structureManager->getElementById($record['brandId'])) {
                    $result[] = $brandElement;
                    $sort[] = $brandElement->title;
                }
            }
            array_multisort($sort, SORT_ASC, $result);
        }
        return $result;
    }

    public function getProductsListDiscounts()
    {
        $result = [];
        /**
         * @var shoppingBasketDiscounts $shoppingBasketDiscounts
         */
        $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
        if ($discountsList = $shoppingBasketDiscounts->getApplicableDiscountsList()) {
            //todo: check discounts by using checkProductsListIfApplicable?
            $result = $discountsList;
        }

        return $result;
    }

    public function getProductsListCategories()
    {
        $result = [];
        $productIdsQuery = clone $this->getFilteredProductsQuery();
        $db = $this->getService('db');
        if ($records = $db->table('structure_links')
            ->select('parentStructureId')->distinct()
            ->whereIn('childStructureId', $productIdsQuery)
            ->where('type', '=', 'catalogue')
            ->get()) {
            $structureManager = $this->getService('structureManager');
            $sort = [];
            foreach ($records as $record) {
                if ($categoryElement = $structureManager->getElementById($record['parentStructureId'])) {
                    $result[] = $categoryElement;
                    $sort[] = $categoryElement->title;
                }
            }
            array_multisort($sort, SORT_ASC, $result);
        }
        return $result;
    }

    public function getCacheKey()
    {
        if ($this->cacheKey === null) {
            $this->cacheKey = $this->getFilterPriceString();
            $this->cacheKey .= implode(',', $this->getFilterDiscountIds());
            $this->cacheKey .= implode(',', $this->getFilterBrandIds());
            $this->cacheKey .= implode(',', $this->getFilterCategoryIds());
            $this->cacheKey .= implode(',', $this->getFilterParameterValueIds());
            $this->cacheKey .= implode(',', $this->getFilterAvailability());
            $this->cacheKey .= $this->getFilterOrder();
            $this->cacheKey .= $this->getFilterSort();
            $this->cacheKey .= $this->getFilterLimit();
        }
        return $this->cacheKey;
    }
}