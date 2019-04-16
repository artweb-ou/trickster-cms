<?php

/**
 * Class selectedProductsElement
 *
 * @property int $amountOnPageEnabled
 */
class selectedProductsElement extends ProductsListStructureElement implements ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    use ConnectedIconsProviderTrait;
    public $dataResourceName = 'module_selected_products';
    public $defaultActionName = 'show';
    public $role = 'content';
    public $productsList;
    protected $connectedCategories;
    protected $connectedCategoriesIds;
    protected $connectedBrands;
    protected $connectedBrandsIds;
    protected $connectedDiscounts;
    protected $connectedDiscountsIds;
    protected $connectedProductsIds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['selectionType'] = 'text'; // manual (1) or automatic (0)
        $moduleStructure['autoSelectionType'] = 'text';
        $moduleStructure['amount'] = 'text';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['productsLayout'] = 'text';
        $moduleStructure['products'] = 'numbersArray';

        // filters related:
        $moduleStructure['filterCategory'] = 'checkbox';
        $moduleStructure['filterBrand'] = 'checkbox';
        $moduleStructure['filterPrice'] = 'checkbox';
        $moduleStructure['filterDiscount'] = 'checkbox';
        $moduleStructure['availabilityFilterEnabled'] = 'checkbox';
        $moduleStructure['priceInterval'] = 'naturalNumber';
        $moduleStructure['priceSortingEnabled'] = 'text';
        $moduleStructure['nameSortingEnabled'] = 'text';
        $moduleStructure['dateSortingEnabled'] = 'text';
        $moduleStructure['amountOnPageEnabled'] = 'checkbox';
        // not stored in db ---------------------------------------------------
        $moduleStructure['discountsIds'] = 'numbersArray';
        $moduleStructure['categoriesIds'] = 'numbersArray';
        $moduleStructure['brandsIds'] = 'numbersArray';
        $moduleStructure['productSelectionIds'] = 'numbersArray';
        $moduleStructure['iconIds'] = 'numbersArray';
        // filters related:
        $moduleStructure['parametersIds'] = 'numbersArray';
        $moduleStructure['catalogueFilterId'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showLayoutForm',
            'showFilters',
            'showPositions',
            'showPrivileges',
        ];
    }

    protected function getProductsListBaseQuery()
    {
//        $result = $this->getActiveProductsIds();
//
//        if ($result && $limitingCategoryIds = $this->getConnectedCategoriesIds()) {
//            $structureManager = $this->getService('structureManager');
//            $subCategoriesIdIndex = [];
//            foreach ($limitingCategoryIds as &$categoryId) {
//                if ($category = $structureManager->getElementById($categoryId)) {
//                    $category->gatherSubCategoriesIdIndex($category->id, $subCategoriesIdIndex);
//                }
//            }
//            $limitingCategoryIds = array_keys($subCategoriesIdIndex);
//            $conditions = [];
//            $conditions[] = [
//                'type',
//                '=',
//                'catalogue',
//            ];
//            if ($limitingCategoryIds) {
//                $conditions[] = [
//                    'parentStructureId',
//                    'in',
//                    $limitingCategoryIds,
//                ];
//            }
//            $conditions[] = [
//                'childStructureId',
//                'in',
//                $result,
//            ];
//            $result = [];
//            if ($records = persistableCollection::getInstance('structure_links')
//                ->conditionalLoad('childStructureId', $conditions)
//            ) {
//                foreach ($records as &$record) {
//                    $result[] = $record['childStructureId'];
//                }
//            }
//        }
//        if ($result && $limitingDiscountIds = $this->getConnectedDiscountsIds()) {
//            $basketDiscounts = $this->getService('shoppingBasketDiscounts');
//            $discountsProductsIds = [];
//            foreach ($limitingDiscountIds as &$discountId) {
//                if ($discount = $basketDiscounts->getDiscount($discountId)) {
//                    $discountsProductsIds = array_merge($discount->getApplicableProductsIds(), $discountsProductsIds);
//                }
//            }
//            $result = array_intersect($result, $discountsProductsIds);
//        }
//
//        if ($result && $limitingIconIds = $this->getConnectedIconsIds()) {
//            /**
//             * @var ProductIconsManager $productIconsManager
//             */
//            $productIconsManager = $this->getService('ProductIconsManager');
//            $connectedIconsProducts = [];
//            foreach ($limitingIconIds as $iconId) {
//                if ($iconProducts = $productIconsManager->getIconProductIds($iconId)) {
//                    $connectedIconsProducts = array_merge($iconProducts, $connectedIconsProducts);
//                }
//            }
//            $result = array_intersect($result, $connectedIconsProducts);
//        }
//        if ($result && $limitingSelectionsIds = $this->getConnectedProductSelectionIds()) {
//            $collection = persistableCollection::getInstance('module_product_parameter_value');
//            $conditions = [
//                [
//                    'value',
//                    'in',
//                    $limitingSelectionsIds,
//                ],
//                [
//                    'productId',
//                    'in',
//                    $result,
//                ],
//            ];
//            $result = [];
//            if ($records = $collection->conditionalLoad('productId', $conditions)) {
//                foreach ($records as &$record) {
//                    $result[] = $record['productId'];
//                }
//            }
//        }
//        return array_unique($result);
    }

    public function getProductSelectionParameters()
    {
        $selectionParameters = [];
        $structureManager = $this->getService('structureManager');
        if ($productSelectionElements = $structureManager->getElementsByType('productSelection')) {
            $connectedProductSelectionIds = $this->getConnectedProductSelectionIds();
            foreach ($productSelectionElements as &$productSelectionElement) {
                $selectionId = $productSelectionElement->id;
                $selectionParameters[$selectionId] = [
                    'id' => $productSelectionElement->id,
                    'title' => $productSelectionElement->title,
                    'structureName' => $productSelectionElement->structureName,
                    'select' => in_array($productSelectionElement->id, $connectedProductSelectionIds),
                ];
                //                $productSelectionOptions = $structureManager->getElementsChildren($selectionId);
                //
                //                foreach ($productSelectionOptions as &$productSelectionOption) {
                //                    $selectionParameters[$selectionId]['options'][] = array(
                //                        'id'        => $productSelectionOption->id,
                //                        'title'     => $productSelectionOption->title,
                //                        'select'    => in_array($productSelectionOption->id, $connectedProductSelectionIds)
                //                    );
                //                }
            }
        }
        return $selectionParameters;
    }

    public function getConnectedProductSelectionIds()
    {
        return $this->getService('linksManager')
            ->getConnectedIdList($this->id, 'selectedProductsProductSelection', 'parent');
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
    }

    public function getConnectedCategories()
    {
        if (is_null($this->connectedCategories)) {
            $this->connectedCategories = [];
            if ($categoryIds = $this->getConnectedCategoriesIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($categoryIds as &$categoryId) {
                    if ($categoryId && $categoryElement = $structureManager->getElementById($categoryId)) {
                        $item = [];
                        $item['id'] = $categoryElement->id;
                        $item['title'] = $categoryElement->getTitle();
                        $item['select'] = true;
                        $this->connectedCategories[] = $item;
                    }
                }
            }
        }
        return $this->connectedCategories;
    }

    function getConnectedCategoriesIds()
    {
        if (is_null($this->connectedCategoriesIds)) {
            $this->connectedCategoriesIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, 'selectedProductsCategory', 'parent');
        }
        return $this->connectedCategoriesIds;
    }

    public function getConnectedBrands()
    {
        if (is_null($this->connectedBrands)) {
            $this->connectedBrands = [];
            if ($brandIds = $this->getConnectedBrandsIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($brandIds as &$brandId) {
                    if ($brandId && $brandElement = $structureManager->getElementById($brandId)) {
                        $item = [];
                        $item['id'] = $brandElement->id;
                        $item['title'] = $brandElement->getTitle();
                        $item['select'] = true;
                        $this->connectedBrands[] = $item;
                    }
                }
            }
        }
        return $this->connectedBrands;
    }

    function getConnectedBrandsIds()
    {
        if (is_null($this->connectedBrandsIds)) {
            $this->connectedBrandsIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, 'selectedProductsBrand', 'parent');
        }
        return $this->connectedBrandsIds;
    }

    public function getConnectedDiscounts()
    {
        if (is_null($this->connectedDiscounts)) {
            $this->connectedDiscounts = [];
            if ($discountIds = $this->getConnectedDiscountsIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($discountIds as &$discountId) {
                    if ($discountId && $discountElement = $structureManager->getElementById($discountId)) {
                        $item = [];
                        $item['id'] = $discountElement->id;
                        $item['title'] = $discountElement->getTitle();
                        $item['select'] = true;
                        $this->connectedDiscounts[] = $item;
                    }
                }
            }
        }
        return $this->connectedDiscounts;
    }

    public function getConnectedDiscountsIds()
    {
        if (is_null($this->connectedDiscountsIds)) {
            $this->connectedDiscountsIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, 'selectedProductsDiscount', 'parent');
        }
        return $this->connectedDiscountsIds;
    }

    public function getSelectionIdsForFiltering()
    {
        $result = [];
        $connectedIds = $this->getConnectedParametersIds();
        if ($connectedIds) {
            $availableIds = parent::getSelectionIdsForFiltering();
            if ($availableIds) {
                $result = array_intersect($connectedIds, $availableIds);
            }
        }
        return $result;
    }

    public function getProductsList()
    {
        if ($this->productsList !== null) {
            return $this->productsList;
        }
        $this->productsList = [];
        $arguments = $this->getFilterArguments();
        $structureManager = $this->getService('structureManager');
        $languagesManager = $this->getService('languagesManager');
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        if ($this->selectionType) { // manual selection - prepare the connected products
            $linksManager = $this->getService('linksManager');
            if ($connectedProductLinks = $linksManager->getElementsLinks($this->id, 'selectedProducts', 'parent')) {
                $productValues = [];
                $sortingRequired = ($arguments['sort'] !== 'manual');

                foreach ($connectedProductLinks as &$link) {
                    if ($productElement = $structureManager->getElementById($link->childStructureId, $currentLanguageId)
                    ) {
                        if ($productElement->inactive == '0' && ($productElement->isPurchasable() || $productElement->availability == 'inquirable')) {
                            $this->productsList[] = $productElement;
                            if ($sortingRequired) {
                                $productValues[] = $productElement->$arguments['sort'];
                            }
                        }
                    }
                }
                if ($sortingRequired) {
                    array_multisort($productValues, constant('SORT_' . strtoupper($arguments['order'])), $this->productsList);
                }
            }
        } else { // automatic sorting, prepare products list depending on the autoSelectionType
            $availableProductIds = $this->getProductsListBaseQuery();
            if ($availableProductIds) {
//                if ($this->isFilterable()) {
//                    $arguments = $this->getFilterArguments();
//                    $this->getFilters($arguments);
//                    if ($this->baseFilter !== null) {
//                        $this->baseFilter->apply($availableProductIds);
//                    }
//                }
                $db = $this->getService('db');
                if ($availableProductIds) {
                    switch ($this->autoSelectionType) {
                        // date
                        case 0:
                            $collection = persistableCollection::getInstance('structure_elements');
                            $conditions = [
                                [
                                    'structureType',
                                    '=',
                                    'product',
                                ],
                                [
                                    'id',
                                    'in',
                                    $availableProductIds,
                                ],
                            ];
                            $this->productsList = [];
                            $limitFields = [];
                            if ($this->amount != '') {
                                $limitFields = [
                                    0,
                                    $this->amount,
                                ];
                            }

                            $orderFields = ['dateCreated' => 'desc'];
                            $availableProductIds = [];
                            if ($records = $collection->conditionalLoad('distinct(id)', $conditions, $orderFields, $limitFields, [], true)
                            ) {
                                foreach ($records as &$record) {
                                    $availableProductIds[] = $record['id'];
                                }
                            }
                            break;

                        // popularity (purchases)
                        case 1:
                            $sql = $db->table('module_product')
                                ->distinct()
                                ->select('id')
                                ->whereIn('id', $availableProductIds)
                                ->orderBy('purchaseCount', 'desc');

                            $this->productsList = [];
                            if ($this->amount) {
                                $sql->limit($this->amount);
                            }

                            $availableProductIds = $sql->pluck('id');

                            break;

                        // purchased latest
                        case 2:
                            $sql = $db->table('module_product')
                                ->distinct()
                                ->select('id')
                                ->whereIn('id', $availableProductIds)
                                ->orderBy('lastPurchaseDate', 'desc');

                            $this->productsList = [];
                            if ($this->amount) {
                                $sql->limit($this->amount);
                            }

                            $availableProductIds = $sql->pluck('id');

                            break;

                        // random discounted products
                        case 3:
                            $sql = $db->table('module_product')
                                ->distinct()
                                ->select('id')
                                ->where('oldPrice', '<>', 0)
                                ->whereIn('id', $availableProductIds)
                                ->inRandomOrder();

                            $this->productsList = [];
                            if ($this->amount) {
                                $sql->limit($this->amount);
                            }

                            $availableProductIds = $sql->pluck('id');
                            break;
                        // all available products
                        case 4:
                            shuffle($availableProductIds);
                            if (is_numeric($this->amount)) {
                                $availableProductIds = array_slice($availableProductIds, 0, (int)$this->amount);
                            }
                            break;
                        default:
                            break;
                    }
                }
            }

            if ($availableProductIds) {
                $elementsOnPage = $arguments['limit'];
                if ($arguments['sort'] === 'manual') {
                    $pageNumber = max(1, (int)controller::getInstance()->getParameter('page'));
                    $pager = new pager($this->generatePagerUrl(), count($availableProductIds), $elementsOnPage, $pageNumber, 'page');
                    $this->productsPager = $pager;
                    $availableProductIds = array_slice($availableProductIds, ($pageNumber - 1) * $elementsOnPage, $elementsOnPage);
                    $this->getService('ParametersManager')->preloadPrimaryParametersForProducts($availableProductIds);
                    foreach ($availableProductIds as &$productId) {
                        if ($product = $structureManager->getElementById($productId, $currentLanguageId)) {
                            $this->productsList[] = $product;
                        }
                    }
                } else {
                    $pager = new pager($this->generatePagerUrl(), count($availableProductIds), $elementsOnPage, (int)controller::getInstance()
                        ->getParameter('page'), 'page');
                    $this->productsPager = $pager;

                    $this->getService('ParametersManager')->preloadPrimaryParametersForProducts($availableProductIds);

                    $query = $this->getService('db')->table('module_product')->select('module_product.id')->distinct();
                    $query->whereIn('module_product.id', $availableProductIds);
                    $query->skip($pager->startElement);
                    $query->limit($elementsOnPage);

                    if ($arguments['sort'] == 'date') {
                        $query->join('structure_elements', 'module_product.id', '=', 'structure_elements.id', 'left');
                        $query->orderBy('structure_elements.dateCreated', $arguments['order']);
                    } else {
                        $query->orderBy($arguments['sort'], $arguments['order']);
                    }

                    if ($records = $query->get()) {
                        foreach ($records as &$record) {
                            if ($product = $structureManager->getElementById($record['id'], $currentLanguageId)) {
                                $this->productsList[] = $product;
                            }
                        }
                    }
                }
            }
        }

        return $this->productsList;
    }

    // productsearch functions

    public function getCatalogue()
    {
        if ($this->catalogue === null) {
            $this->catalogue = false;
            if ($connectedCataloguesIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, 'selectedProductsCatalogue', 'parent')
            ) {
                $this->catalogue = $this->getService('structureManager')->getElementById($connectedCataloguesIds[0]);
            }
        }
        return $this->catalogue;
    }

    public function getConnectedParametersIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'selectedProductsParameter', 'parent');
    }

    public function getConnectedParameters()
    {
        if ($this->connectedParameters === null) {
            $this->connectedParameters = [];
            if ($connectedParametersIds = $this->getConnectedParametersIds()) {
                $this->connectedParameters = $this->getService('structureManager')
                    ->getElementsByIdList($connectedParametersIds, $this->id);
            }
        }
        return $this->connectedParameters;
    }

    public function getConnectedProductsIds()
    {
        if ($this->connectedProductsIds === null) {
            $this->connectedProductsIds = [];
            $collection = persistableCollection::getInstance('structure_links');
            $catalogueConditions = [];
            $categoryConditions = [
                [
                    'type',
                    '=',
                    'catalogue',
                ],
            ];
            // check category (& subcategories)
            if ($limitingCategoryIds = $this->getConnectedCategoriesIds()) {
                $structureManager = $this->getService('structureManager');
                $subCategoriesIdIndex = [];
                foreach ($limitingCategoryIds as &$categoryId) {
                    if ($category = $structureManager->getElementById($categoryId)) {
                        $category->gatherSubCategoriesIdIndex($category->id, $subCategoriesIdIndex);
                    }
                }
                $limitingCategoryIds = array_keys($subCategoriesIdIndex);

                if ($limitingCategoryIds) {
                    $categoryConditions[] = [
                        'parentStructureId',
                        'in',
                        $limitingCategoryIds,
                    ];
                }
            } else {
                $catalogueConditions = [
                    [
                        'type',
                        '=',
                        'productCatalogueProduct',
                    ],
                ];
            }
            $conditions = [$categoryConditions, $catalogueConditions];

            if ($records = $collection->conditionalOrLoad('childStructureId', $conditions)) {
                foreach ($records as &$record) {
                    $this->connectedProductsIds[] = $record['childStructureId'];
                }
                $this->connectedProductsIds = array_unique($this->connectedProductsIds);

                // check discounts
                if ($limitingDiscountIds = $this->getConnectedDiscountsIds()) {
                    $conditions = [
                        [
                            'type',
                            '=',
                            'discountProduct',
                        ],
                        [
                            'childStructureId',
                            'in',
                            $this->connectedProductsIds,
                        ],
                        [
                            'parentStructureId',
                            'in',
                            $limitingDiscountIds,
                        ],
                    ];
                    if ($records = $collection->conditionalLoad('childStructureId', $conditions)) {
                        $this->connectedProductsIds = [];
                        foreach ($records as &$record) {
                            $this->connectedProductsIds[] = $record['childStructureId'];
                        }
                    }
                }

                // check parameters
                if ($limitingSelectionsIds = $this->getConnectedProductSelectionIds()) {
                    $collection = persistableCollection::getInstance('module_product_parameter_value');
                    $conditions = [
                        [
                            'value',
                            'in',
                            $limitingSelectionsIds,
                        ],
                        [
                            'productId',
                            'in',
                            $this->connectedProductsIds,
                        ],
                    ];
                    $this->connectedProductsIds = [];
                    if ($records = $collection->conditionalLoad('productId', $conditions)) {
                        foreach ($records as &$record) {
                            $this->connectedProductsIds[] = $record['productId'];
                        }
                    }
                }

                if ($this->connectedProductsIds) {
                    // now, check stock conditions...

                    $collection = persistableCollection::getInstance('module_product');
                    // filter out products that are out of stock/unavailable
                    $conditions = [
                        [
                            'availability',
                            '=',
                            'quantity_dependent',
                        ],
                        [
                            'quantity',
                            '=',
                            '0',
                        ],
                    ];

                    if ($records = $collection->conditionalLoad('id', $conditions)) {
                        $unavailableProductsIds = [];
                        foreach ($records as &$record) {
                            $unavailableProductsIds[] = $record['id'];
                        }
                        $this->connectedProductsIds = array_diff($this->connectedProductsIds, $unavailableProductsIds);
                    }

                    if ($this->connectedProductsIds) {
                        $conditions = [
                            [
                                'availability',
                                '!=',
                                'unavailable',
                            ],
                            [
                                'id',
                                'in',
                                $this->connectedProductsIds,
                            ],
                        ];
                        $this->connectedProductsIds = [];
                        if ($records = $collection->conditionalLoad('id', $conditions)) {
                            foreach ($records as &$record) {
                                $this->connectedProductsIds[] = $record['id'];
                            }
                        }
                    }
                    $this->connectedProductsIds = array_unique($this->connectedProductsIds);
                }
            }
        }
        return $this->connectedProductsIds;
    }

    public function getDiscounts()
    {
        return $this->getService('structureManager')
            ->getElementsByType('discount', $this->getService('languagesManager')
                ->getCurrentLanguageId());
    }

    public function getBrands()
    {
        return $this->getService('structureManager')->getElementsByType('brand', $this->getService('languagesManager')
            ->getCurrentLanguageId());
    }

    protected function isFilterableByType($filterType)
    {
        switch ($filterType) {
            case 'category':
                $result = ($this->filterCategory && $this->getCategoriesInfo());
                break;
            case 'brand':
                $result = ($this->filterBrand && $this->getBrandsList());
                break;
            case 'discount':
                $result = ($this->filterDiscount && $this->getDiscountsList());
                break;
            case 'parameter':
                $result = (bool)$this->getFilterSelections();
                break;
            case 'price':
                $result = $this->filterPrice;
                break;
            case 'availability':
                $result = $this->availabilityFilterEnabled;
                break;
            default:
                $result = true;
        }

        return $result;
    }

    public function isAmountSelectionEnabled()
    {
        return $this->amountOnPageEnabled;
    }
}


