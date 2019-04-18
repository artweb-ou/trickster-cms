<?php

class catalogueElement extends structureElement
{
//    use ProductFilterFactoryTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['product'];
    public $defaultActionName = 'showFullList';
    public $role = 'container';
    protected $productsPageList;
    public $pager;
    public $productsList;
    public $requestArguments;
    protected $filteredIds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        // temporary fields
        $moduleStructure['elements'] = 'array';
        $moduleStructure['massEditMethod'] = 'text';
        $moduleStructure['newCategories'] = 'array';
        $moduleStructure['newDiscounts'] = 'array';
        $moduleStructure['newBrand'] = 'text';
        $moduleStructure['targets'] = 'text';
        $moduleStructure['targetAll'] = 'checkbox';
        $moduleStructure['productPriceMultiplier'] = 'floatNumber';
        $moduleStructure['productPriceAddition'] = 'floatNumber';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function getFiltrationUrl()
    {
        $controller = $this->getService('controller');
        return $controller->fullURL . '?' . $_SERVER['QUERY_STRING'];
    }

    public function massModifyProducts()
    {
        $multiplier = (float)$this->productPriceMultiplier;
        $addition = (float)$this->productPriceAddition;

        $db = $this->getService('db');
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        if ($this->targetAll) {
            $targets = $this->getAdminProductsPageFilteredIds();
        } elseif ($this->targets) {
            $targets = explode(',', $this->targets);
        }
        $replacing = $this->massEditMethod === 'replace';
        foreach ($targets as $productId) {
            if ($this->newCategories) {
                if ($replacing) {
                    $oldConnectedIds = $linksManager->getConnectedIdList($productId, 'catalogue', 'child');
                    foreach ($oldConnectedIds as &$oldConnectedId) {
                        $linksManager->unLinkElements($oldConnectedId, $productId, 'catalogue');
                    }
                }
                foreach ($this->newCategories as $categoryId) {
                    $linksManager->linkElements($categoryId, $productId, 'catalogue');
                }
            }
            if ($this->newDiscounts) {
                if ($replacing) {
                    $oldConnectedIds = $linksManager->getConnectedIdList($productId, 'discountProduct', 'child');
                    foreach ($oldConnectedIds as &$oldConnectedId) {
                        $linksManager->unLinkElements($oldConnectedId, $productId, 'discountProduct');
                    }
                }
                foreach ($this->newDiscounts as $discountId) {
                    $linksManager->linkElements($discountId, $productId, 'discountProduct');
                }
            }
            if ($this->newBrand) {
                $oldConnectedIds = $linksManager->getConnectedIdList($productId, 'productbrand', 'child');
                foreach ($oldConnectedIds as &$oldConnectedId) {
                    $linksManager->unLinkElements($oldConnectedId, $productId, 'productbrand');
                }
                $linksManager->linkElements($this->newBrand, $productId, 'productbrand');
            }
        }
        if ($multiplier || $addition) {
            if (is_numeric($multiplier) && $multiplier > 0 && $multiplier != 0) {
                $query = $db->table('module_product')
                    ->whereIn('id', $targets);
                $query->update(['price' => $query->raw('`price`*' . $multiplier)]);

                $query = $db->table('module_product_selection_pricing')
                    ->whereIn('productId', $targets);
                $query->update(['price' => $query->raw('`price`*' . $multiplier)]);
            }
            if (is_numeric($addition) && $addition != 0) {
                $query = $db->table('module_product')
                    ->whereIn('id', $targets);
                $query->update(['price' => $query->raw('`price`+' . $addition)]);

                $query = $db->table('module_product_selection_pricing')
                    ->whereIn('productId', $targets);
                $query->update(['price' => $query->raw('`price`+' . $addition)]);
            }
        }
    }

    protected function getAdminProductsPageFilteredIds()
    {
        if ($this->filteredIds === null) {
            $this->filteredIds = [];
            $controller = $this->getService('controller');
            $db = $this->getService('db');
            $records = $db->table('module_product')->select('id')->distinct()->get('id');
            foreach ($records as $record) {
                $this->filteredIds[] = $record['id'];
            }
            $filtering = !!$controller->getParameter('filter');
            if ($filtering) {
                if ($filterArguments = $controller->getParameter('category')) {
                    $filter = $this->createProductFilter('category', $filterArguments);
                    $filter->filter($this->filteredIds);
                }
                if ($filterArguments = $controller->getParameter('brand')) {
                    $filter = $this->createProductFilter('brand', $filterArguments);
                    $filter->filter($this->filteredIds);
                }
                if ($filterArguments = $controller->getParameter('discount')) {
                    $filter = $this->createProductFilter('discount', $filterArguments);
                    $filter->filter($this->filteredIds);
                }
            }
        }
        return $this->filteredIds;
    }

    public function getProductsPage($elementsOnPage = 100)
    {
        if ($this->productsPageList === null) {
            $arguments = $this->parseRequestArguments();
            $this->productsPageList = [];

            $db = $this->getService('db');
            $controller = $this->getService('controller');
            $allIds = $this->getAdminProductsPageFilteredIds();
            $filtering = !!$controller->getParameter('filter');
            if ($filtering) {
                $allIds = $this->getAdminProductsPageFilteredIds();
            }
            $collection = persistableCollection::getInstance('module_product');
            if ($filtering) {
                $elementsCount = count($allIds);
            } else {
                $elementsCount = $db->table('module_product')->distinct()->count('id');
            }
            if ($elementsCount > 0) {
                $pager = new pager($this->makePagerUrl($arguments), $elementsCount, $elementsOnPage, $arguments['page'], 'page', 2, true);
                $this->pager = $pager;
                $orderField = $arguments['order']['field'];
                $table = 'module_product';
                if ($orderField == 'dateModified') {
                    $table = 'structure_elements';
                }
                $query = $db->table($table)->select('id');
                $query->orderBy($orderField, $arguments['order']['argument']);

                if ($orderField != 'dateModified') {
                    $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
                    $publicLanguageId = $this->getService('languagesManager')->getCurrentLanguageId($marker);
                    //get ordered id list where appropriate translation exists
                    $translatedIDs = [];
                    if ($records = $collection->conditionalLoad(['id'], [
                        'languageId',
                        '=',
                        $publicLanguageId,
                    ])
                    ) {
                        foreach ($records as &$record) {
                            $translatedIDs[] = $record['id'];
                        }
                    }
                    $query->where(function ($query) use ($translatedIDs, $publicLanguageId) {
                        return $query->where(
                            function ($query) use ($translatedIDs, $publicLanguageId) {
                                return $query->whereIn('id', $translatedIDs)
                                    ->where('languageId', $publicLanguageId);
                            }
                        )->orWhere(function ($query) use ($translatedIDs, $publicLanguageId) {
                            return $query->whereNotIn('id', $translatedIDs);
                        });
                    });
                }
                if ($arguments['order']['field'] == 'dateModified') {
                    $query->where('structureType', 'product');
                }
                if ($filtering) {
                    $query->whereIn('id', $allIds);
                }
                $query->skip($pager->startElement)->take($elementsOnPage);
                $records = $query->get('id');
                $structureManager = $this->getService('structureManager');
                if ($records) {
                    $productsIds = [];
                    foreach ($records as &$record) {
                        $productsIds[] = $record['id'];
                    }
                    $collection = persistableCollection::getInstance('import_origin');
                    $conditions = [
                        [
                            'elementId',
                            'IN',
                            $productsIds,
                        ],
                    ];
                    $records = $collection->conditionalLoad(['importOrigin', 'elementId'], $conditions);
                    $productsOriginsIndex = [];
                    if ($records) {
                        foreach ($records as &$record) {
                            $productId = $record['elementId'];
                            $importOrigin = $record['importOrigin'];
                            if (!isset($productsOriginsIndex[$productId])) {
                                $productsOriginsIndex[$productId] = [];
                            }
                            $productsOriginsIndex[$productId][] = $importOrigin;
                        }
                    }
                    $conditions = [
                        [
                            'childStructureId',
                            'IN',
                            $productsIds,
                        ],
                    ];
                    $collection = persistableCollection::getInstance('structure_links');
                    $records = $collection->conditionalLoad([
                        'parentStructureId',
                        'childStructureId',
                    ], $conditions);
                    $productBrandIdIndex = [];
                    foreach ($records as &$record) {
                        $productBrandIdIndex[$record['childStructureId']] = $record['parentStructureId'];
                    }
                    $this->productsPageList = $structureManager->getElementsByIdList($productsIds, $this->id, 'idlist');
                    foreach ($this->productsPageList as $product) {
                        $productId = $product->id;
                        $product->setXmlSourcesCodeNames(isset($productsOriginsIndex[$productId])
                            ? $productsOriginsIndex[$productId]
                            : []);
                        $product->setBrandsIdList(isset($productBrandIdIndex[$productId])
                            ? [$productBrandIdIndex[$productId]]
                            : []);
                    }
                }
            }
        }
        return $this->productsPageList;
    }

    protected function makePagerUrl(array &$requestArguments)
    {
        $url = $this->URL;
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
            'page' => (int)$controller->getParameter('page'),
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
                'field' => $orderField,
                'argument' => $orderArgument,
            ];
        } else {
            $this->requestArguments['order'] = [
                'field' => 'title',
                'argument' => 'asc',
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
        return $this->URL . '?' . http_build_query($parameters);
    }

    public function getRequestArguments()
    {
        return $this->requestArguments;
    }

    public function getExportLink()
    {
        $controller = $this->getService('controller');
        $categories = $controller->getParameter('category');
        $brands = $controller->getParameter('brand');
        $discounts = $controller->getParameter('discount');

        $link = $this->URL . 'id:' . $this->id . '/action:xlsExport';

        if ($categories || $brands || $discounts) {
            $link .= '?filter=1';
        }

        if ($categories) {
            foreach ($categories as $category) {
                $link .= '&category[]=' . $category;
            }
        }
        if ($brands) {
            foreach ($brands as $brand) {
                $link .= '&brand[]=' . $brand;
            }
        }
        if ($discounts) {
            foreach ($discounts as $discount) {
                $link .= '&discount[]=' . $discount;
            }
        }

        return $link;
    }
}
