<?php

/**
 * Class categoryElement
 *
 * @property string $deliveryPriceType
 */
class categoryElement extends categoryStructureElement implements ConfigurableLayoutsProviderInterface, ImageUrlProviderInterface, ColumnsTypeProvider
{
    use ImageUrlProviderTrait;
    use ConfigurableLayoutsProviderTrait;
    use DeliveryPricesTrait;
    use EventLoggingElementTrait;

    public $dataResourceName = 'module_category';
    protected $allowedTypes = ['category'];
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $parentCategoriesList;
    protected $categoriesList;
    /**
     * @var productCatalogueElement
     */
    protected $currentProductCatalogue;
    public $feedbackFormsList = [];
    public $columns;
    public $level = 0;
    protected $parametersGroups;
    protected $discountsList;
    protected $iconsList;
    protected $iconsCompleteList;
    protected $parentCategory;
    protected $parentCategories;
    protected $topProductsList;
    protected $mainParentCategory;
    protected $usedParametersIds;
    protected $selectedUsedParameters;
    protected $productsPager;
    protected $requestArguments;
    protected $featuredProducts;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['content'] = 'html';
        $moduleStructure['parameters'] = 'array';
        $moduleStructure['image'] = 'image';

        //layouts
        $moduleStructure['categoryLayout'] = 'text';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['productsLayout'] = 'text';
        $moduleStructure['colorLayout'] = 'text';
        $moduleStructure['productsMobileLayout'] = 'text';
        $moduleStructure['categoriesMobileLayout'] = 'text';

        $moduleStructure['deliveryStatus'] = 'text';
        $moduleStructure['deliveryPriceType'] = 'text';
        $moduleStructure['formDeliveries'] = 'array';
        $moduleStructure['feedbackId'] = 'text';

        $moduleStructure['defaultOrder'] = 'text';
        // manul, price, price;desc, title, title;desc
        $moduleStructure['manualSortingEnabled'] = 'text';
        // 0 - inherit, 1 - enabled, 2 - disabled
        $moduleStructure['priceSortingEnabled'] = 'text';
        $moduleStructure['nameSortingEnabled'] = 'text';
        $moduleStructure['dateSortingEnabled'] = 'text';
        $moduleStructure['brandFilterEnabled'] = 'text';
        $moduleStructure['parameterFilterEnabled'] = 'text';
        $moduleStructure['discountFilterEnabled'] = 'text';
        $moduleStructure['availabilityFilterEnabled'] = 'text';
        $moduleStructure['amountOnPageEnabled'] = 'text';

        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';

        $moduleStructure['hidden'] = 'checkbox';

        // temporary fields
        $moduleStructure['elements'] = 'array';
        $moduleStructure['productCataloguesIds'] = 'array';
        $moduleStructure['parentCategoriesIds'] = 'array';
        $moduleStructure['importInfo'] = 'array';

        $moduleStructure['connectedIconIds'] = 'array';

        $moduleStructure['unit'] = 'text';

        $moduleStructure['metaDescriptionTemplate'] = 'text';
        $moduleStructure['metaTitleTemplate'] = 'text';
        $moduleStructure['metaSubTitleTemplate'] = 'text';

        $moduleStructure['metaH1Template'] = 'text';
        $moduleStructure['h1'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'content';
        $multiLanguageFields[] = 'introduction';
        $multiLanguageFields[] = 'feedbackId';
        $multiLanguageFields[] = 'metaTitle';
        $multiLanguageFields[] = 'metaDescription';
        $multiLanguageFields[] = 'deliveryStatus';
        $multiLanguageFields[] = 'unit';
        $multiLanguageFields[] = 'metaDescriptionTemplate';
        $multiLanguageFields[] = 'metaTitleTemplate';
        $multiLanguageFields[] = 'metaH1Template';
        $multiLanguageFields[] = 'h1';
        $multiLanguageFields[] = 'metaSubTitleTemplate';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showTexts',
            'showSortingFilter',
            'showLayoutForm',
            'showSubCategoriesForm',
            'showProductsForm',
            'showSeoForm',
            'showDelivery',
            'showIconForm',
            'showImportForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    /**
     * @return array|mixed|null
     */
    public function getConnectedProductsIds()
    {
        if (!is_null($this->connectedProductsIds)) {
            return $this->connectedProductsIds;
        }
        $this->connectedProductsIds = [];

        $subCategoriesIdIndex = [];
        $this->gatherSubCategoriesIdIndex($this->id, $subCategoriesIdIndex);

        // Get all connected products' ids
        if (count($subCategoriesIdIndex)) {
            $collection = persistableCollection::getInstance('structure_links');

            $conditions = [];
            if ($this->getService('ConfigManager')->get('main.displaySubCategoryProducts')) {
                $conditions[] = [
                    'parentStructureId',
                    'in',
                    array_keys($subCategoriesIdIndex),
                ];
            } else {
                $conditions[] = [
                    'parentStructureId',
                    '=',
                    array_keys($subCategoriesIdIndex)[0],
                ];
            }

            $conditions[] = [
                'type',
                '=',
                'catalogue',
            ];
            if ($records = $collection->conditionalLoad('childStructureId', $conditions)) {
                foreach ($records as &$record) {
                    $this->connectedProductsIds[] = $record['childStructureId'];
                }
            }

            $this->connectedProductsIds = array_unique($this->connectedProductsIds);
        }

        return $this->connectedProductsIds;
    }

    protected function getProductsListBaseQuery()
    {
        if ($this->productsListBaseQuery !== null) {
            return $this->productsListBaseQuery;
        }
        $this->productsListBaseQuery = false;

        $query = $this->getProductsQuery();

        $query->leftJoin('structure_links', 'module_product.id', '=', 'childStructureId');

        //include only the products connected to this category or include all subcategories as well
        if ($this->getService('ConfigManager')->get('main.displaySubCategoryProducts')) {
            $subCategoriesIdIndex = [];
            $this->gatherSubCategoriesIdIndex($this->id, $subCategoriesIdIndex);
            $query->whereIn('parentStructureId', array_keys($subCategoriesIdIndex));
        } else {
            $query->where('parentStructureId', '=', $this->id);
        }
        $query->where('type', '=', 'catalogue');

        $this->productsListBaseQuery = $query;
        return $this->productsListBaseQuery;
    }

    public function getMainParentCategory()
    {
        if (is_null($this->mainParentCategory)) {
            if ($mainParent = $this->getParentCategory()) {
                for (; ;) {
                    if ($parentCategory = $mainParent->getParentCategory()) {
                        $mainParent = $parentCategory;
                    } else {
                        break;
                    }
                }
            } else {
                $mainParent = $this;
            }
            $this->mainParentCategory = $mainParent;
        }
        return $this->mainParentCategory;
    }

    public function gatherSubCategoriesIdIndex($categoryId, &$index)
    {
        $index[$categoryId] = true;

        $linksManager = $this->getService('linksManager');
        $idList = $linksManager->getConnectedIdList($categoryId, 'structure', 'parent', false);
        foreach ($idList as &$subCategoryId) {
            $this->gatherSubCategoriesIdIndex($subCategoryId, $index);
        }
    }

    public function getProductsByIdList(&$productIdIndex)
    {
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        $idList = $linksManager->getConnectedIdList($this->id, 'catalogue', 'parent', false);

        $productIdFilter = array_intersect(array_keys($productIdIndex), $idList);

        $productsList = $structureManager->getElementsByIdList($productIdFilter, $this->id, true);
        foreach ($productsList as &$product) {
            if (isset($productIdIndex[$product->id])) {
                unset($productIdIndex[$product->id]);
            }
        }
        return $productsList;
    }

    public function getFeaturedProducts()
    {
        if ($this->featuredProducts === null) {
            $this->featuredProducts = [];
            $structureManager = $this->getService('structureManager');

            $subCategoriesIdIndex = [];
            $this->gatherSubCategoriesIdIndex($this->id, $subCategoriesIdIndex);

            $filteredIds = [];
            if (count($subCategoriesIdIndex)) {
                $collection = persistableCollection::getInstance("structure_links");

                $conditions = [];
                $conditions[] = [
                    "parentStructureId",
                    "in",
                    array_keys($subCategoriesIdIndex),
                ];
                $conditions[] = [
                    "type",
                    "=",
                    "catalogue",
                ];

                if ($records = $collection->conditionalLoad("childStructureId", $conditions)) {
                    foreach ($records as &$record) {
                        $filteredIds[] = $record["childStructureId"];
                    }
                }
            }
            // filter out products that are out of stock
            $collection = persistableCollection::getInstance("module_product");
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
            if ($records = $collection->conditionalLoad('id', $conditions)) {
                $unavailableProductsIds = [];
                foreach ($records as &$record) {
                    $unavailableProductsIds[] = $record['id'];
                }
                $filteredIds = array_diff($filteredIds, $unavailableProductsIds);
            }

            if ($filteredIds) {
                $languagesManager = $this->getService('LanguagesManager');
                $languageId = $languagesManager->getCurrentLanguageId();
                $conditions = [
                    [
                        'showincategory',
                        '=',
                        '1',
                    ],
                    [
                        "id",
                        "in",
                        $filteredIds,
                    ],
                    [
                        "inactive",
                        "!=",
                        "1",
                    ],
                    [
                        "availability",
                        "!=",
                        "unavailable",
                    ],
                    [
                        "languageId",
                        "=",
                        $languageId,
                    ],
                ];
                $orderFields = [
                    'purchaseCount' => 'desc',
                ];
                $productIdFilter = [];

                if ($records = $collection->conditionalLoad('id', $conditions, $orderFields, 5)) {
                    foreach ($records as &$record) {
                        $productIdFilter[] = $record['id'];
                    }
                }
                $productIdIndex = array_flip($productIdFilter);

                foreach ($subCategoriesIdIndex as $categoryId => &$value) {
                    if (count($productIdIndex) > 0) {
                        if ($category = $structureManager->getElementById($categoryId)) {
                            $this->featuredProducts = array_merge($this->featuredProducts,
                                $category->getProductsByIdList($productIdIndex));
                        }
                    }
                }
                $this->featuredProducts = $structureManager->getElementsByIdList($productIdFilter, $this->id, true);
            }
        }
        return $this->featuredProducts;
    }

    public function getTopProductsList($limit = 5)
    {
        if (is_null($this->topProductsList)) {
            $this->topProductsList = [];
            $structureManager = $this->getService('structureManager');

            $subCategoriesIdIndex = [];
            $this->gatherSubCategoriesIdIndex($this->id, $subCategoriesIdIndex);

            $filteredIds = [];
            if (count($subCategoriesIdIndex)) {
                $collection = persistableCollection::getInstance("structure_links");

                $conditions = [];
                $conditions[] = [
                    "parentStructureId",
                    "in",
                    array_keys($subCategoriesIdIndex),
                ];
                $conditions[] = [
                    "type",
                    "=",
                    "catalogue",
                ];

                if ($records = $collection->conditionalLoad("childStructureId", $conditions)) {
                    foreach ($records as &$record) {
                        $filteredIds[] = $record["childStructureId"];
                    }
                }
            }

            // filter out products that are out of stock
            $collection = persistableCollection::getInstance("module_product");
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
            if ($records = $collection->conditionalLoad('id', $conditions)) {
                $unavailableProductsIds = [];
                foreach ($records as &$record) {
                    $unavailableProductsIds[] = $record['id'];
                }
                $filteredIds = array_diff($filteredIds, $unavailableProductsIds);
            }

            if ($filteredIds) {
                $languagesManager = $this->getService('LanguagesManager');
                $languageId = $languagesManager->getCurrentLanguageId();
                $conditions = [
                    [
                        "id",
                        "in",
                        $filteredIds,
                    ],
                    [
                        "inactive",
                        "!=",
                        "1",
                    ],
                    [
                        "availability",
                        "!=",
                        "unavailable",
                    ],
                    [
                        "languageId",
                        "=",
                        $languageId,
                    ],
                ];
                $orderFields = [
                    'showincategory' => 'desc',
                    'purchaseCount'  => 'desc',
                ];
                $productIdFilter = [];

                if ($records = $collection->conditionalLoad('id', $conditions, $orderFields, $limit)) {
                    foreach ($records as &$record) {
                        $productIdFilter[] = $record['id'];
                    }
                }
                $productIdIndex = array_flip($productIdFilter);
                $this->topProductsList = [];

                foreach ($subCategoriesIdIndex as $categoryId => &$value) {
                    if (count($productIdIndex) > 0) {
                        if ($category = $structureManager->getElementById($categoryId)) {
                            $this->topProductsList = array_merge($this->topProductsList,
                                $category->getProductsByIdList($productIdIndex));
                        }
                    }
                }
                $this->topProductsList = $structureManager->getElementsByIdList($productIdFilter, $this->id, true);
            }
        }
        return $this->topProductsList;
    }

    /**
     * @param null $limit
     * @return categoryElement[]
     */
    public function getChildCategories($limit = null)
    {
        $childCategories = [];
        $structureManager = $this->getService('structureManager');
        $childrenList = $structureManager->getElementsChildren($this->id, 'container');
        foreach ($childrenList as &$element) {
            if ($element->structureType == 'category') {
                $childCategories[] = $element;
            }
            if (!empty($limit) && count($childCategories) >= $limit) {
                break;
            }
        }
        return $childCategories;
    }

    public function getResidingCategories()
    {
        $residingCategories = [];
        $structureManager = $this->getService('structureManager');
        if ($parentElement = $this->getCurrentParentElement()) {
            $childrenList = $structureManager->getElementsChildren($parentElement->id);
            foreach ($childrenList as &$element) {
                if ($element->structureType == 'category' && ($element->level == $this->level) && ($element->id != $this->id)
                ) {
                    $residingCategories[] = $element;
                }
            }
        }
        return $residingCategories;
    }

    public function getTreeFilters()
    {
        $filters = [];
        for ($category = $this; $category != false; $category = $category->getParentCategory()) {
            $filters[] = $category->makeCategoryFilters($category);
        }
        $filters = array_reverse($filters);
        if ($children = $this->getChildCategories()) {
            $filters[] = $children[0]->makeCategoryFilters($children[0]);
        }
        return $filters;
    }

    public function makeCategoryFilters($category)
    {
        $arguments = $this->getFilterArguments();

        $categoriesIds = [];
        $categoriesIds[] = $category->id;

        if ($neighbours = $category->getResidingCategories()) {
            foreach ($neighbours as &$neighbour) {
                $categoriesIds[] = $neighbour->id;
            }
        }
        $categoryArguments = array_intersect($arguments['category'], $categoriesIds);
        return $this->createProductFilter('category', $categoryArguments, $categoriesIds);
    }

    public function getDiscounts()
    {
        if (is_null($this->discountsList)) {
            $structureManager = $this->getService('structureManager');

            $languagesManager = $this->getService('LanguagesManager');
            $this->discountsList = $structureManager->getElementsByType("discount",
                $languagesManager->getCurrentLanguageId());
        }
        return $this->discountsList;
    }

    /**
     * @return categoryElement[]
     */
    public function getParentCategories()
    {
        if ($this->parentCategories === null) {
            $this->parentCategories = [];
            $structureManager = $this->getService('structureManager');
            if ($parentElements = $structureManager->getElementsParents($this->id)) {
                foreach ($parentElements as &$parent) {
                    if ($parent->structureType == "category") {
                        $this->parentCategories[] = $parent;
                    }
                }
            }
        }
        return $this->parentCategories;
    }

    /**
     * @return bool|categoryElement
     */
    public function getParentCategory()
    {
        if ($this->parentCategory === null) {
            $this->parentCategory = false;
            if ($parentElements = $this->getParentCategories()) {
                foreach ($parentElements as $parent) {
                    if (!$this->parentCategory) {
                        $this->parentCategory = $parent;
                    }
                    if ($parent->requested) {
                        $this->parentCategory = $parent;
                        break;
                    }
                }
            }
        }
        return $this->parentCategory;
    }

    /**
     * Recursively checks if a setting is enabled
     * @param $settingName
     * @return bool
     */
    public function isSettingEnabled($settingName)
    {
        $enabled = false;
        switch ($this->$settingName) {
            case 0:
                if ($parent = $this->getParentCategory()) {
                    $enabled = $parent->isSettingEnabled($settingName);
                } elseif ($this->currentProductCatalogue) {
                    $enabled = $this->currentProductCatalogue->isSettingEnabled($settingName);
                } else {
                    $enabled = false;
                }
                break;
            case 1:
                $enabled = true;
                break;
            case 2:
                $enabled = false;
        }
        return $enabled;
    }

    public function getCategoriesList()
    {
        if (is_null($this->categoriesList)) {
            $this->categoriesList = [];
            $structureManager = $this->getService('structureManager');
            if ($childrenList = $structureManager->getElementsChildren($this->id, 'container')) {
                foreach ($childrenList as &$element) {
                    if ($element->structureType == 'category' && !$element->hidden) {
                        $this->categoriesList[] = $element;
                    }
                }
            }
        }
        return $this->categoriesList;
    }

    public function getInheritableProperty($propertyName)
    {
        $propertyValue = $this->$propertyName;
        if (!$propertyValue || $propertyValue == 'inherit') {
            if ($parentCategory = $this->getParentCategory()) {
                $propertyValue = $parentCategory->getInheritableProperty($propertyName);
            } elseif (is_object($this->currentProductCatalogue)) {
                $propertyValue = $this->currentProductCatalogue->$propertyName;
            }
        }
        return $propertyValue;
    }

    final public function getPropertyFromAncestorCategory($propertyName)
    {
        $propertyValue = false;
        $iteratedCategory = $this;

        while (!$propertyValue && ($parentCategory = $iteratedCategory->getParentCategory())) {
            $propertyValue = $parentCategory->$propertyName;
            $iteratedCategory = $parentCategory;
        }
        if (!$propertyValue && $this->currentProductCatalogue) {
            $propertyValue = $this->currentProductCatalogue->$propertyName;
        }
        return $propertyValue;
    }

    public function setProductCatalogue($element)
    {
        $this->currentProductCatalogue = $element;
    }

    public function getAdminIconsList()
    {
        if ($this->iconsList === null) {
            /**
             * @var ProductIconsManager $productIconsManager
             */
            $productIconsManager = $this->getService('ProductIconsManager');
            $this->iconsList = $productIconsManager->getOwnIcons($this->id, $this->structureType);
        }
        return $this->iconsList;
    }

    /**
     * Returns the layout to be used for subcategories
     * @return string
     */
    public function getLayout()
    {
        if (controller::getInstance()->getParameter('productsearch')) {
            $layout = 'hide';
        } else {
            if (!is_null($this->categoryLayout) && $this->categoryLayout != "" && $this->categoryLayout != "inherit") {
                $layout = $this->categoryLayout;
            } else {
                $layout = $this->getInheritableProperty("categoryLayout");
            }
            if (!$layout) {
                $layout = $this->getService('ConfigManager')->get('main.templateTypeCategorySubCategory');
            }
        }
        return $layout;
    }

    public function getProductsLayout()
    {
        if (!is_null($this->productsLayout) && $this->productsLayout != '' && $this->productsLayout != 'inherit') {
            $productsLayout = $this->productsLayout;
        } else {
            $productsLayout = $this->getInheritableProperty('productsLayout');
        }
        if (!$productsLayout || ($productsLayout == 'hide' && controller::getInstance()
                    ->getParameter('productsearch'))
        ) {
            $productsLayout = $this->getService('ConfigManager')->get('main.templateTypeCategoryProduct');
        }
        return $productsLayout;
    }

    public function getTemplate($viewName = null)
    {
        $applicationName = controller::getInstance()->getApplicationName();
        if ($applicationName == "public") {
            if ($this->final) {
                $template = "category.details.tpl";
            } else {
                if (is_null($viewName) || !$viewName) {
                    if ($this->currentProductCatalogue && $this->currentProductCatalogue->categoryLayout) {
                        $viewName = $this->currentProductCatalogue->categoryLayout;
                    }
                    if (!$viewName || $viewName == 'hide') {
                        $viewName = "wide";
                    }
                }
                $template = "category." . $viewName . ".tpl";
            }
        } elseif ($applicationName == "mobile") {
            if ($this->final) {
                $template = "category.details.tpl";
            } else {
                if (is_null($viewName) || !$viewName) {
                    $viewName = "short";
                }
                $template = "category." . $viewName . ".tpl";
            }
        } else {
            $template = parent::getTemplate();
        }
        return $template;
    }

    // admin app related -------------------------------------
    public function getAdminProductsList()
    {
        if ($this->productsList == null) {
            $this->productsList = [];
            $arguments = $this->parseRequestArguments();
            if ($productIds = $this->getConnectedProductsIds()) {
                /**
                 * @var \Illuminate\Database\Connection $db
                 */
                $db = $this->getService('db');

                $elementsOnPage = 50;
                $pager = new pager($this->makeAdminPagerUrl(), count($productIds), $elementsOnPage, $arguments['page'],
                    'page', 2, true);
                $this->productsPager = $pager;

                $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
                $publicLanguageId = $this->getService('LanguagesManager')->getCurrentLanguageId($marker);
                $query = $db->table('module_product');

                if ($arguments['order']['field'] != 'dateModified' && $arguments['order']['field'] != 'dateCreated') {
                    //get ordered id list where appropriate translation exists
                    $translatedIDs = [];

                    if ($records = $db->table('module_product')->where('languageId', '=',
                        $publicLanguageId)->whereIn('id', $productIds)->select(['id'])->get()) {
                        $translatedIDs = array_column($records, 'id');
                    }
                    $query->orWhere(function ($query) use ($translatedIDs, $productIds, $publicLanguageId) {
                        $query->whereIn('module_product.id', $translatedIDs);
                        $query->whereIn('module_product.id', $productIds);
                        $query->where('module_product.languageId', '=', $publicLanguageId);
                    });
                    $query->orWhere(function ($query) use ($translatedIDs, $productIds, $publicLanguageId) {
                        $query->whereNotIn('module_product.id', $translatedIDs);
                        $query->whereIn('module_product.id', $productIds);
                    });
                }

                if ($arguments['order']['field'] == 'dateModified' || $arguments['order']['field'] == 'dateCreated') {
                    $query->leftJoin('structure_elements', 'structure_elements.id', '=', 'module_product.id');
                }
                $query->orderBy($arguments['order']['field'], $arguments['order']['argument']);

                $query->limit($elementsOnPage);
                $query->offset($pager->startElement);
                $query->select(['module_product.id'])->distinct();

                $structureManager = $this->getService('structureManager');
                if ($records = $query->get()) {
                    foreach ($records as &$record) {
                        if ($product = $structureManager->getElementById($record['id'])) {
                            $this->productsList[] = $product;
                        }
                    }
                }
            }
        }
        return $this->productsList;
    }

    protected function makeAdminPagerUrl()
    {
        $url = $this->URL;
        $url .= 'id:' . $this->id . '/action:' . $this->actionName . '/';
        $params = [];
        $params += $_GET;
        if ($this->requested && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    protected function parseRequestArguments()
    {
        $controller = controller::getInstance();
        $this->requestArguments = [
            'page'  => (int)$controller->getParameter('page'),
            'order' => false,
        ];
        $orderParameter = $controller->getParameter("order");
        if ($orderParameter) {
            $orderParameterParts = explode(',', $orderParameter);
            if (count($orderParameterParts) === 2) {
                $orderField = $orderParameterParts[0];
                $orderArgument = $orderParameterParts[1];
            } else {
                $orderField = $orderParameter;
                $orderArgument = 'asc';
            }
            $this->requestArguments['order'] = [
                'field'    => $orderField,
                'argument' => $orderArgument,
            ];
        } else {
            $this->requestArguments['order'] = [
                'field'    => 'id',
                'argument' => 'desc',
            ];
        }
        return $this->requestArguments;
    }

    public function getContentListOrderUrl($field)
    {
        $parameters = $_GET;
        if (isset($parameters['page'])) {
            unset($parameters['page']);
        }
        $parameters['order'] = $field;
        $activeOrderInfo = $this->requestArguments['order'];
        if ($activeOrderInfo && $activeOrderInfo['field'] == $field && $activeOrderInfo['argument'] == 'asc') {
            // reverse order
            $parameters['order'] .= ',desc';
        }
        return $this->getUrl('showProductsForm') . '?' . http_build_query($parameters);
    }

    public function getRequestArguments()
    {
        return $this->requestArguments;
    }

    public function getConnectedCatalogueFoldersIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'catalogue', 'child');
    }

    /**
     * @return categoryElement[]
     */
    public function getParentCategoriesList()
    {
        $linksManager = $this->getService('linksManager');
        $structureManager = $this->getService('structureManager');
        $parentCategoryLinks = $linksManager->getElementsLinksIndex($this->id, 'structure', 'child');

        $this->parentCategoriesList = [];
        if ($categoriesFolder = $structureManager->getElementByMarker('categories')) {
            $categoriesList = $structureManager->getElementsFlatTree($categoriesFolder->id, 'container');

            foreach ($categoriesList as &$category) {
                $categoryItem = [];
                $categoryItem['level'] = $category->level - 3;
                if (isset($parentCategoryLinks[$category->id])) {
                    $categoryItem['select'] = true;
                } else {
                    $categoryItem['select'] = false;
                }
                $categoryItem['title'] = $category->getTitle();
                $categoryItem['id'] = $category->id;

                $this->parentCategoriesList[] = $categoryItem;
            }
        }
        return $this->parentCategoriesList;
    }

    public function deleteElementData()
    {
        $collection = persistableCollection::getInstance('import_origin');
        $searchFields = ['elementId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        $collection = persistableCollection::getInstance('category_import_pricing');
        $searchFields = ['categoryId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        parent::deleteElementData();
    }

    public function getSelectionsIdsConnectedForFiltering()
    {
        $result = [];
        if ($parentCategory = $this->getParentCategory()) {
            $result = array_merge($result, $parentCategory->getSelectionsIdsConnectedForFiltering());
        }
        $result = array_merge($result, parent::getSelectionsIdsConnectedForFiltering());
        return $result;
    }

    public function getColumnsType()
    {
        if ($parent = $this->getParentCategory()) {
            return $parent->getColumnsType();
        } elseif ($this->currentProductCatalogue) {
            return $this->currentProductCatalogue->getColumnsType();
        }
        return false;
    }

    public function getAllowedTypes($currentAction = 'showFullList')
    {
        if ($currentAction == 'showProductsForm') {
            $this->allowedTypes = ['product'];
        } elseif ($currentAction == 'showIconForm') {
            $this->allowedTypes = ['genericIcon'];
        }
        return parent::getAllowedTypes($currentAction);
    }

    public function getConnectedGenericIconList()
    {
        $linksManager = $this->getService('linksManager');
        $connectedIds = $linksManager->getConnectedIdList($this->id, 'genericIconCategory');
        return $connectedIds;
    }

    public function getGenericIconList()
    {
        $genericIconList = [];
        $structureManager = $this->getService('structureManager');
        $connectedIcons = $this->getConnectedGenericIconList();
        $genericIcons = $structureManager->getElementsByType('genericIcon');
        foreach ($genericIcons as $genericIcon) {
            $genericIconList[] = [
                'id'     => $genericIcon->id,
                'title'  => $genericIcon->getTitle(),
                'select' => in_array($genericIcon->id, $connectedIcons)
            ];
        }
        return $genericIconList;
    }

    public function getFeedbackFormList()
    {
        $structureManager = $this->getService('structureManager');
        $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
        $publicRoot = $structureManager->getElementByMarker($marker);
        $languages = $structureManager->getElementsChildren($publicRoot->id);
        $feedbackFormsList = array();
        foreach ($languages as &$languageElement) {
            $selectedId = $this->getValue('feedbackId', $languageElement->id);
            $feedbackFormsList[$languageElement->id] = [];
            $elementsList = $structureManager->getElementsByType("feedback", $languageElement->id);
            foreach ($elementsList as &$element) {
                if ($element->structureType == 'feedback') {
                    $field = [];
                    $field['id'] = $element->id;
                    $field['title'] = $element->getTitle();
                    $field['select'] = $selectedId;

                    $feedbackFormsList[$languageElement->id][] = $field;
                }
            }
        }
        return $feedbackFormsList;
    }

    public function getProductCataloguesIds()
    {
        $structureManager = $this->getService('structureManager');
        $productCataloguesIds = [];
        $connectedFoldersIds = $this->getConnectedCatalogueFoldersIds();
        $allCatalogues = $structureManager->getElementsByType('productCatalogue');
        if ($allCatalogues) {
            foreach ($allCatalogues as &$catalogueElement) {
                if ($catalogueElement->categorized && !$catalogueElement->connectAllCategories) {
                    if ($parentElement = $catalogueElement->getContainerElement()) {
                        $field = [];
                        $field['id'] = $catalogueElement->id;
                        $field['title'] = $catalogueElement->getTitle();
                        $field['select'] = in_array($parentElement->id, $connectedFoldersIds);

                        $productCataloguesIds[] = $field;
                    }
                }
            }
        }
        return $productCataloguesIds;
    }

    public function getExpectedField($type)
    {
        if ($type === 'texts') {
            return [
                'content',
                'introduction',
            ];
        }
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return str_repeat('&nbsp;&nbsp;&nbsp;', $this->level);
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }
}
