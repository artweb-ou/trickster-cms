<?php

class productsImportManager implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $brandsIndex;
    protected $parameterGroupsIndex;
    protected $importOriginProductsIndex;
    protected $importOrigin;
    protected $importCategoriesInfo;
    protected $languageCode;
    protected $originsCollection;
    protected $elementImportIdIndex;
    protected $rulesElements;
    protected $categoriesInfoList;
    protected $productImportCategoriesInfo;
    /**
     * @var productsImportManager
     */
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new productsImportManager();
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::$instance = $this;
        $pathsManager = $this->getService('PathsManager');
        $path = $pathsManager->getIncludeFilePath('modules/transportObjects/pdoTransport.class.php');
        include_once($path);

        $this->originsCollection = persistableCollection::getInstance('import_origin');
        $uploadsCachePath = $pathsManager->getPath('uploadsCache');
        $pathsManager->ensureDirectory($uploadsCachePath);
        $uploadsPath = $pathsManager->getPath('uploads');
        $pathsManager->ensureDirectory($uploadsPath);
    }

    /**
     * This method selects the language for the current import procedure, which can be changed at any time.
     *
     * @param bool|string $code - 'eng', 'est' and so on, if set to false - then import overwrites every language's info
     */
    public function setImportLanguageCode($code)
    {
        $this->languageCode = $code;
        $languagesManager = $this->getService('languagesManager');
        $languagesManager->setCurrentLanguageCode($code);
    }

    /**
     * Quick method to control if the product with this import code exists
     *
     * @param string $importId
     * @param string $importOrigin
     * @return bool
     */
    public function checkProductExistance($importId, $importOrigin)
    {
        $result = false;

        $existingProductsIndex = $this->getExistingProductsIndex($importOrigin);
        if (isset($existingProductsIndex[$importId])) {
            $result = true;
        }
        return $result;
    }

    public function importCategory($categoryImportId, $parentCategoryId, $importOrigin)
    {
        if ($importOrigin) {
            $categoryId = $this->getCategoryElementIdByImportId($categoryImportId, $importOrigin);
        } else {
            $categoryId = $this->getCategoryElementIdByTitle($categoryImportId, $parentCategoryId);
        }
        if ($categoryId == 0) {
            return $this->createCategory($categoryImportId, $parentCategoryId, $importOrigin);
        }
        return $this->getService('structureManager')->getElementById($categoryId);
    }

    /**
     * @deprecated - use importCategory instead
     */
    public function importCategoryToParent($categoryImportId, $parentCategoryId, $importOrigin)
    {
        return $this->importCategory($categoryImportId, $parentCategoryId, $importOrigin);
    }

    protected function createCategory($categoryImportId, $parentCategoryId, $origin)
    {
        $result = false;
        $structureManager = $this->getService('structureManager');
        if (!($parentElement = $structureManager->getElementById($parentCategoryId))) {
            $categoriesId = $structureManager->getElementIdByMarker('categories');
            $parentElement = $structureManager->getElementById($categoriesId);
        }
        if ($parentElement) {
            $element = $structureManager->createElement('category', 'show', $parentElement->id);
            $element->prepareActualData();
            $element->title = $categoryImportId;
            $element->structureName = $categoryImportId;
            $element->persistElementData();

            $this->recordElementImport($element->id, $categoryImportId, $origin);

            $element->persistElementData();

            if (is_array($this->importCategoriesInfo)) {
                if (!isset($this->importCategoriesInfo[$origin])) {
                    $this->importCategoriesInfo[$origin] = [];
                }
                $this->importCategoriesInfo[$origin][$element->id] = [
                    $categoryImportId,
                ];
            }
            $result = $element;
        }
        return $result;
    }

    /**
     * Finds a category by an import ID, links it with the product
     * @param $productImportId
     * @param $categoryImportId
     * @param $importOrigin
     */
    public function checkCategoryLinkByImportId($productImportId, $categoryImportId, $importOrigin)
    {
        if ($importOrigin) {
            if ($categoryId = $this->getCategoryElementIdByImportId($categoryImportId, $importOrigin)) {
                $this->checkCategoryLink($productImportId, $categoryId, $importOrigin);
            }
        } else {
            if ($categoryId = $this->getCategoryElementIdByTitle($categoryImportId)) {
                $this->checkCategoryLink($productImportId, $categoryId, $importOrigin);
            }
        }
    }

    /**
     * Finds a product by an import ID, links it with the product
     * @param $productImportId
     * @param $connectedImportIdList
     * @param $importOrigin
     *
     */
    public function checkConnectedProductsLinks($productImportId, $connectedImportIdList, $importOrigin)
    {
        $existingProductsIndex = $this->getExistingProductsIndex($importOrigin);
        foreach ($connectedImportIdList as &$connectedImportId) {
            if (isset($existingProductsIndex[$productImportId]) && isset($existingProductsIndex[$connectedImportId])) {
                $this->linkProductWithProduct($existingProductsIndex[$productImportId]['id'], $existingProductsIndex[$connectedImportId]['id']);
            }
        }
    }

    /**
     * Quick check if imported product is connected to category
     * If it's not connected, then a link is created
     *
     * @param string $productImportId
     * @param string $categoryId
     * @param string $importOrigin
     */
    public function checkCategoryLink(&$productImportId, &$categoryId, &$importOrigin)
    {
        $existingProductsIndex = $this->getExistingProductsIndex($importOrigin);
        if (isset($existingProductsIndex[$productImportId])) {
            $this->linkProductWithCategory($existingProductsIndex[$productImportId]['id'], $categoryId);
        }
    }

    /**
     * Returns an array of existing products information for a selected import origin
     *
     * @param string $importOrigin
     * @return array|boolean
     */
    public function getExistingProductsIndex($importOrigin)
    {
        if (!isset($this->importOriginProductsIndex[$importOrigin])) {
            $this->importOriginProductsIndex[$importOrigin] = [];

            $importedElementsIds = $this->getAllImportElementsIdsByOrigin($importOrigin);
            if ($importedElementsIds) {
                $collection = persistableCollection::getInstance('module_product');
                $conditions = [
                    [
                        'id',
                        'IN',
                        $importedElementsIds,
                    ],
                ];
                $columns = [
                    'id',
                    'importId',
                    'availability',
                    'price',
                    'quantity',
                    'inactive',
                ];
                if ($records = $collection->conditionalLoad($columns, $conditions)) {
                    $productsInfo = [];
                    foreach ($records as &$record) {
                        if ($importId = $this->getElementImportId($record['id'], $importOrigin)) {
                            $productsInfo[$importId] = $record;
                        }
                    }
                    $this->importOriginProductsIndex[$importOrigin] = $productsInfo;
                }
            }
        }
        return $this->importOriginProductsIndex[$importOrigin];
    }

    /**
     * Gets short category info for selected import id and origin
     * @param string $categoryImportId
     * @param string $importOrigin
     * @return bool|array
     */
    public function getImportCategoryInfo($categoryImportId, $importOrigin)
    {
        $result = false;
        $categoriesInfo = $this->getImportCategoriesInfo();
        if (isset($categoriesInfo[$importOrigin]) && isset($categoriesInfo[$importOrigin][$categoryImportId])) {
            $result = $categoriesInfo[$importOrigin][$categoryImportId];
        }
        return $result;
    }

    /**
     * Creates a link between product and category. Part of checkCategoryLink procedure
     *
     * @param int $productId
     * @param int $categoryId
     */
    protected function linkProductWithCategory($productId, $categoryId)
    {
        $linksManager = $this->getService('linksManager');
        $linksManager->linkElements($categoryId, $productId, 'catalogue');
    }

    /**
     * Creates a link between two products. Part of checkConnectedProductsLinks procedure
     *
     * @param $product1Id
     * @param $product2Id
     * @internal param int $product1Id
     * @internal param int $product2Id
     */
    protected function linkProductWithProduct($product1Id, $product2Id)
    {
        $linksManager = $this->getService('linksManager');
        $linksManager->linkElements($product1Id, $product2Id, 'connected', true);
    }

    /**
     * Creates a link between parameter and category. Part of importParameterInfo procedure
     *
     * @param int $parameterId
     * @param int $categoryId
     */
    public function linkParameterWithCategory($parameterId, $categoryId)
    {
        $linksManager = $this->getService('linksManager');
        $linksManager->linkElements($categoryId, $parameterId, 'categoryParameter');
    }

    /**
     * Finds an existing parameter by its elementId and updates it
     * @param $parametersData
     * @param $productImportId
     * @param $categoryId
     * @param $importOrigin
     */
    public function importInfoForExistingParameters($parametersData, $productImportId, $categoryId, $importOrigin)
    {
        $structureManager = $this->getService('structureManager');
        foreach ($parametersData as $parameterData) {
            $parameter = false;
            if ($element = $structureManager->getElementById($parameterData["elementId"])) {
                if ($element->structureType == 'productParameter') {
                    $parameter = $element;
                    $this->saveParameterValue($parameterData['value'], $parameter, $productImportId, $importOrigin);
                } elseif ($element->structureType == 'productSelection') {
                    $parameter = $element;
                    $this->saveSelectionValue($parameterData['value'], $parameter, $productImportId, $importOrigin);
                } elseif ($element->structureType == 'productSelectionValue') {
                    if ($parameter = $element->getSelectionElement()) {
                        $this->saveSelectionValue($parameterData['value'], $parameter, $productImportId, $importOrigin);
                    }
                }
                if ($parameter) {
                    $this->linkParameterWithCategory($parameter->id, $categoryId);
                }
            }
        }
    }

    /**
     * Imports parameters info into the database
     * @param array $info
     * @param string $productImportId
     * @param string $categoryImportId
     * @param string $importOrigin
     */
    public function importParameterInfo($info, $productImportId, $categoryImportId, $importOrigin)
    {
        $categoryId = $this->getCategoryElementIdByImportId($categoryImportId, $importOrigin);

        foreach ($info as &$parameterInfo) {
            $parameters = $this->getProductParameters($parameterInfo, $importOrigin, $categoryId, $categoryImportId);

            foreach ($parameters as &$parameter) {
                if ($parameter->structureType == 'productParameter') {
                    $this->saveParameterValue($parameterInfo['value'], $parameter, $productImportId, $importOrigin);
                } elseif ($parameter->structureType == 'productSelection') {
                    $this->saveSelectionValue($parameterInfo['value'], $parameter, $productImportId, $importOrigin);
                }
                if ($categoryId > 0) {
                    $this->linkParameterWithCategory($parameter->id, $categoryId);
                }
            }
        }
    }

    /**
     * Imports single parameter info into the database
     * @param array $parameterInfo
     * @param string $productImportId
     * @param string $importOrigin
     * @param $categoryImportId
     * @return bool|\productParameterElement
     */
    public function importSingleParameterInfo($parameterInfo, $productImportId, $importOrigin, $categoryImportId)
    {
        if ($parameter = $this->getProductParameter($parameterInfo, $importOrigin, $categoryImportId)) {
            if ($parameter->structureType == 'productParameter') {
                $this->saveParameterValue($parameterInfo['value'], $parameter, $productImportId, $importOrigin);
            } else {
                if ($parameter->structureType == 'productSelection') {
                    $this->saveSelectionValue($parameterInfo['value'], $parameter, $productImportId, $importOrigin);
                }
            }
        }
        return $parameter;
    }

    public function importMultilanguageParameterInfo($info, $productImportId, $categoryImportId, $importOrigin)
    {
        foreach ($info as &$parameterInfo) {
            if ($parameter = $this->getProductParameter($parameterInfo, $importOrigin, $categoryImportId)) {
                if ($parameter->structureType == 'productParameter') {
                    $this->saveMultiLanguageParameterValue($parameterInfo['value'], $parameter, $productImportId, $importOrigin);
                } elseif ($parameter->structureType == 'productSelection') {
                    $this->saveSelectionValue($parameterInfo['value'], $parameter, $productImportId, $importOrigin);
                }
                if ($categoryId = $this->getCategoryElementIdByImportId($categoryImportId, $importOrigin)) {
                    $this->linkParameterWithCategory($parameter->id, $categoryId);
                }
            }
        }
    }

    public function importSelectionInfo($info, $productImportId, $categoryImportId, $importOrigin)
    {
        foreach ($info as &$parameterInfo) {
            if ($parameter = $this->importSingleSelectionInfo($parameterInfo, $productImportId, $importOrigin, $categoryImportId)) {
                if ($categoryId = $this->getCategoryElementIdByImportId($categoryImportId, $importOrigin)) {
                    $this->linkParameterWithCategory($parameter->id, $categoryId);
                }
            }
        }
    }

    public function importSingleSelectionInfo($parameterInfo, $productImportId, $importOrigin, $categoryImportId)
    {
        if ($parameter = $this->getProductSelection($parameterInfo, $importOrigin, $categoryImportId)) {
            $this->saveSelectionValue($parameterInfo['value'], $parameter, $productImportId, $importOrigin);
        }
        return $parameter;
    }

    /**
     * Checks if a product parameter with import id and origin exists in a database, creates new if required
     * @param array $parameterInfo
     * @param string $importOrigin
     * @param $categoryImportId
     * @return bool|productParameterElement
     */
    protected function getProductParameter($parameterInfo, $importOrigin, $categoryImportId)
    {
        $element = false;
        if ($elementId = $this->getElementIdByImportId($parameterInfo['importId'], $importOrigin)) {
            $element = $this->getService('structureManager')->getElementById($elementId);
        }
        if (!$element) {
            $element = $this->createProductParameter($parameterInfo, $importOrigin, $categoryImportId);
        }
        return $element;
    }

    protected function getProductParameters($parameterInfo, $importOrigin, $categoryId, $categoryImportId)
    {
        $results = [];

        $categoryParametersIds = $this->getService('linksManager')
            ->getConnectedIdList($categoryId, 'categoryParameter', 'parent');
        if ($categoryParametersIds) {
            $parameterImportId = $parameterInfo['importId'];
            $relevantParametersIds = $this->getElementsIdsByImportId($parameterImportId, $importOrigin, $categoryParametersIds);
            foreach ($relevantParametersIds as &$elementId) {
                $element = $this->getService('structureManager')->getElementById($elementId);
                if ($element) {
                    $results[] = $element;
                }
            }
        }
        if (!$results) {
            $results[] = $this->createProductParameter($parameterInfo, $importOrigin, $categoryImportId);
        }
        return $results;
    }

    protected function getProductSelection($parameterInfo, $importOrigin, $categoryImportId)
    {
        $element = false;
        if ($elementId = $this->getElementIdByImportId($parameterInfo['importId'], $importOrigin)) {
            $element = $this->getService('structureManager')->getElementById($elementId);
        }
        if (!$element) {
            $element = $this->createProductSelection($parameterInfo, $importOrigin, $categoryImportId);
        }
        return $element;
    }

    protected function createProductSelection($info, $origin, $categoryImportId)
    {
        $result = false;
        if ($group = $this->getProductParameterGroup($origin, $categoryImportId)) {
            $structureManager = $this->getService('structureManager');
            $element = $structureManager->createElement('productSelection', 'show', $group->id);
            $element->prepareActualData();

            $element->title = $info['title'];
            $element->structureName = $info['title'];
            $element->primary = 1;
            $element->type = 0;
            $element->persistElementData();
            $this->recordElementImport($element->id, $info['importId'], $info['importOrigin']);

            $result = $element;
        }
        return $result;
    }

    /**
     * Saves text parameter value into the database.
     * @param string $value
     * @param structureElement $parameter
     * @param string $productImportId
     * @param string $importOrigin
     */
    protected function saveParameterValue($value, $parameter, $productImportId, $importOrigin)
    {
        if ($product = $this->getProductElement($productImportId, $importOrigin)) {
            $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
            $valueRecord = $valuesCollection->loadObject([
                'parameterId' => $parameter->id,
                'productId' => $product->id,
                'languageId' => 0,
            ]);
            if (!$valueRecord) {
                if ($parameter->single) {
                    $valueRecord = $valuesCollection->getEmptyObject();
                    $valueRecord->languageId = 0;
                    $valueRecord->parameterId = $parameter->id;
                    $valueRecord->productId = $product->id;
                    $valueRecord->value = $value;
                    $valueRecord->persist();
                } else {
                    $languagesManager = $this->getService('languagesManager');
                    $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
                    foreach ($languagesManager->getLanguagesIdList($marker) as $languageId) {
                        $valueRecord = $valuesCollection->getEmptyObject();
                        $valueRecord->languageId = $languageId;
                        $valueRecord->parameterId = $parameter->id;
                        $valueRecord->productId = $product->id;
                        $valueRecord->value = $value;
                        $valueRecord->persist();
                    }
                }
            }
        }
    }

    protected function saveMultiLanguageParameterValue(array $valueIndex, $parameter, $productImportId, $importOrigin)
    {
        if ($product = $this->getProductElement($productImportId, $importOrigin)) {
            $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');

            foreach ($valueIndex as $languageId => &$value) {
                $valueRecord = $valuesCollection->getEmptyObject();
                $valueRecord->parameterId = $parameter->id;
                $valueRecord->languageId = $languageId;
                $valueRecord->productId = $product->id;
                $valueRecord->value = $value;
                $valueRecord->persist();
            }
        }
    }

    /**
     * Saves selection parameter value into the database.
     *
     * @param string $value
     * @param structureElement $parameter
     * @param string $productImportId
     * @param string $importOrigin
     */
    protected function saveSelectionValue($value, $parameter, $productImportId, $importOrigin)
    {
        if ($product = $this->getProductElement($productImportId, $importOrigin)) {
            if ($valueElement = $this->getSelectionValueElement($parameter, $value, $importOrigin)) {
                $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
                $existingValueObject = $valuesCollection->loadObject([
                    'parameterId' => $parameter->id,
                    'productId' => $product->id,
                    'languageId' => 0,
                    'value' => $valueElement->id,
                ]);
                if (!$existingValueObject) {
                    $selectedValueObject = $valuesCollection->getEmptyObject();
                    $selectedValueObject->parameterId = $parameter->id;
                    $selectedValueObject->productId = $product->id;
                    $selectedValueObject->languageId = 0;

                    $selectedValueObject->value = $valueElement->id;
                    $selectedValueObject->persist();
                }
            }
        }
    }

    /**
     * Gets selection value from database
     *
     * @param productParameterElement|productSelectionElement $parameter
     * @param string $value
     * @return bool|productSelectionValueElement
     */
    protected function getSelectionValueElement($parameter, $value, $importOrigin)
    {
        $result = false;

        $value = trim($value);
        $lowerValue = mb_strtolower(html_entity_decode($value));
        $structureManager = $this->getService('structureManager');
        $valueElements = $structureManager->getElementsChildren($parameter->id);

        if ($valueElements) {
            $valuesIds = $this->getService('linksManager')->getConnectedIdList($parameter->id, 'structure', 'parent');
            $relatedElementIdMap = array_flip($this->getElementsIdsByImportId($lowerValue, $importOrigin, $valuesIds));
            if (count($relatedElementIdMap) > 0) {
                foreach ($valueElements as &$valueElement) {
                    if (isset($relatedElementIdMap[$valueElement->id])) {
                        $result = $valueElement;
                        break;
                    }
                }
            }
            if (!$result) {
                foreach ($valueElements as &$valueElement) {
                    if (!$valueElement->importKeywords) {
                        continue;
                    }
                    if ($valueElement->excludeImportKeywords) {
                        $excluded = false;
                        $excludeWords = explode(';', html_entity_decode($valueElement->excludeImportKeywords));
                        foreach ($excludeWords as &$word) {
                            $word = trim($word);
                            if ($word != '') {
                                if (strpos($lowerValue, $word) !== false) {
                                    $excluded = true;
                                    break;
                                }
                            }
                        }
                        if ($excluded) {
                            continue;
                        }
                    }
                    $words = explode(';', html_entity_decode($valueElement->importKeywords));
                    foreach ($words as &$word) {
                        $word = trim($word);
                        if ($word != '') {
                            if (strpos($lowerValue, $word) !== false) {
                                $result = $valueElement;
                                break;
                            }
                        }
                    }
                    if ($result) {
                        break;
                    }
                }
            }
        }
        if (!$result) {
            $element = $structureManager->createElement('productSelectionValue', 'show', $parameter->id);
            $element->prepareActualData();
            $element->title = $value;
            $element->structureName = $value;
            $element->persistElementData();
            $this->recordElementImport($element->id, $lowerValue, $importOrigin);
            $result = $element;
        }
        return $result;
    }

    /**
     * Creates a new product parameter element in product parameters grouped marked with requested origin code
     * @param array $info
     * @param string $origin
     * @param $categoryImportId
     * @return bool|productParameterElement
     */
    protected function createProductParameter($info, $origin, $categoryImportId)
    {
        $result = false;
        if ($group = $this->getProductParameterGroup($origin, $categoryImportId)) {
            $structureManager = $this->getService('structureManager');
            $element = $structureManager->createElement('productParameter', 'show', $group->id);
            $element->prepareActualData();
            $element->title = $info['title'];
            $element->structureName = $info['title'];
            $element->single = $info['single'];
            $element->persistElementData();

            $this->recordElementImport($element->id, $info['importId'], $info['importOrigin']);
            $result = $element;
        }
        return $result;
    }

    /**
     * Returns product parameters group marked with requested import origin
     * @param string $origin
     * @param $categoryImportId
     * @return bool|productParametersGroupElement
     */
    protected function getProductParameterGroup($origin, $categoryImportId)
    {
        $origin = strtolower($origin);
        $categoryImportId = strtolower($categoryImportId);
        $suitableGroup = false;
        $structureManager = $this->getService('structureManager');
        if (!isset($this->parameterGroupsIndex[$origin])) {
            $this->parameterGroupsIndex[$origin] = [];

            $importedElementsIds = $this->getAllImportElementsIdsByOrigin($origin);
            $collection = persistableCollection::getInstance('module_product_parameters_group');
            $conditions = [
                [
                    'id',
                    'IN',
                    $importedElementsIds,
                ],
            ];
            $records = $collection->conditionalLoad([
                'id',
            ], $conditions);
            if ($records) {
                foreach ($records as &$record) {
                    $elementId = $record['id'];
                    $importId = $this->getElementImportId($elementId, $origin);
                    $element = $structureManager->getElementById($elementId);
                    if ($element && $importId) {
                        $this->parameterGroupsIndex[$origin][strtolower($importId)] = $element;
                    }
                }
            }
        }
        if (isset($this->parameterGroupsIndex[$origin][$origin])) {
            $suitableGroup = $this->parameterGroupsIndex[$origin][$origin];
        } elseif (isset($this->parameterGroupsIndex[$origin][$categoryImportId])) {
            $suitableGroup = $this->parameterGroupsIndex[$origin][$categoryImportId];
        }
        if (!$suitableGroup) {
            $parametersElementId = $structureManager->getElementIdByMarker('productparameters');
            if ($parametersElementId) {
                $categoryId = $this->getElementIdByImportId($categoryImportId, $origin);
                $category = $structureManager->getElementById($categoryId);
                $groupTitle = $origin . '-' . $category->title . '-' . $categoryImportId;
                $element = $structureManager->createElement('productParametersGroup', 'show', $parametersElementId);
                $element->prepareActualData();
                $element->title = $groupTitle;
                $element->structureName = $groupTitle;
                $element->persistElementData();
                $this->recordElementImport($element->id, $categoryImportId, $origin);
                $suitableGroup = $element;
                $this->parameterGroupsIndex[$origin][$categoryImportId] = $suitableGroup;
            }
        }
        return $suitableGroup;
    }

    /**
     * Imports images information for a product defined by import id and origin
     *
     * @param array $info
     * @param string $productImportId
     * @param string $importOrigin
     * @param bool $remote
     */
    public function importImages($info, $productImportId, $importOrigin, $remote = false)
    {
        $product = $this->getProductElement($productImportId, $importOrigin);
        if (!$product) {
            return;
        }
        $structureManager = $this->getService('structureManager');

        foreach ($info as &$imageInfo) {
            $imageName = false;
            $imageUrl = '';
            $imagePath = '';
            if ($remote) {
                if (is_array($imageInfo)) {
                    $imageUrl = $imageInfo['url'];
                    $imageName = $imageInfo['fileName'];
                } else {
                    $imageUrl = $imageInfo;
                    if ($urlinfo = parse_url($imageInfo)) {
                        $pathinfo = pathinfo($urlinfo['path']);
                        $imageName = $pathinfo['basename'];
                    }
                }
            } else {
                $imageName = basename($imageInfo);
                $imagePath = $imageInfo;
            }

            // check for existing images
            $duplicate = false;

            if ($productChildElements = $structureManager->getElementsChildren($product->id)) {
                foreach ($productChildElements as $element) {
                    if ($element->structureType == "galleryImage" && $element->originalName == $imageName
                    ) {
                        $duplicate = true;
                        break;
                    }
                }
            }
            if ($duplicate) {
                continue;
            }

            if ($remote) {
                $imagePath = $this->downloadImageFile($imageUrl);
            }

            if ($imageName && $imagePath && file_exists($imagePath)) {
                // create and save
                $element = $structureManager->createElement('galleryImage', 'show', $product->id);
                $element->prepareActualData();
                $pathsManager = $this->getService('PathsManager');
                copy($imagePath, $pathsManager->getPath('uploads') . $element->id);
                $element->image = $element->id;
                $element->originalName = $imageName;
                $element->persistElementData();
            }
        }
    }

    /**
     * Downloads an image from provided URL into temporary folder
     *
     * @param string $url
     * @return bool|string
     */
    protected function downloadImageFile($url)
    {
        $result = false;

        if ($info = parse_url($url)) {
            if ($info['scheme'] == 'ftp') {
                $pathinfo = pathinfo($info['path']);

                $ftpDomain = $info['host'];
                $ftpPath = $pathinfo['dirname'];
                $ftpFile = $pathinfo['basename'];

                if (function_exists('ftp_connect')) {
                    if ($connection = ftp_connect($ftpDomain)) {
                        if (ftp_login($connection, 'COTE_TABLE_PHOTOS', '5f9r6d2')) {
                            ftp_pasv($connection, true);
                            if (ftp_chdir($connection, $ftpPath)) {
                                $filename = $this->getService('PathsManager')->getPath('uploadsCache') . 'importimage';
                                if (ftp_get($connection, $filename, $ftpFile, FTP_BINARY)) {
                                    $result = $filename;
                                }
                            }
                        }
                        ftp_close($connection);
                    }
                }
            } else {
                if (!isset($info['scheme']) || $info['scheme'] == 'http' || $info['scheme'] == 'https') {
                    $url = str_ireplace(' ', '%20', $url);
                    $curl_handle = curl_init();
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 3);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0');

                    if ($data = curl_exec($curl_handle)) {
                        if (($status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE)) == '200') {
                            $filename = $this->getService('PathsManager')->getPath('uploadsCache') . 'importimage';
                            file_put_contents($filename, $data);
                            $result = $filename;
                        }
                    }
                    curl_close($curl_handle);
                }
            }
        }

        return $result;
    }

    /**
     * Imports a specified file
     * @param string $file Full name of the file
     * @param string $fileFieldName Name of the respective field name in the product db
     * @param string $productImportId
     * @param string $importOrigin
     */
    public function importFile(&$file, $fileFieldName, &$productImportId, &$importOrigin)
    {
        if ($product = $this->getProductElement($productImportId, $importOrigin)) {
            if (file_exists($file)) {
                $product->prepareActualData();
                $fileStorageName = $fileFieldName . $product->id;
                $pathsManager = $this->getService('PathsManager');
                copy($file, $pathsManager->getPath('uploads') . $fileStorageName);
                $product->$fileFieldName = $fileStorageName;
                $product->{$fileFieldName . "Name"} = basename($file);
                $product->persistElementData();
            }
        }
    }

    /**
     * Automatically creates structure element for requested product if required and
     * and imports its generic properties
     * @param array $info
     * @param string $importOrigin
     * @return bool|structureElement
     */
    public function importProductInfo($info, $importOrigin)
    {
        $result = false;

        if ($element = $this->getProductElement($info['importId'], $info['importOrigin'])) {
            $languagesManager = $this->getService('languagesManager');
            if ($this->languageCode) {
                $languageIdList = [$languagesManager->getCurrentLanguageId()];
            } else {
                $languageIdList = $languagesManager->getLanguagesIdList();
            }

            $element->prepareActualData();

            $languageData = [];
            $fields = [];
            if (isset($info['title'])) {
                $fields[] = 'title';
                foreach ($languageIdList as &$languageId) {
                    $languageData[$languageId]['title'] = $info['title'];
                }

                $fields[] = 'structureName';
                if (isset($info['structureName'])) {
                    $languageData['structureName'] = $info['structureName'];
                } else {
                    $languageData['structureName'] = $info['title'];
                }
            }
            if (isset($info['content'])) {
                $fields[] = 'content';
                foreach ($languageIdList as &$languageId) {
                    $languageData[$languageId]['content'] = $info['content'];
                }
            }
            if (isset($info['introduction'])) {
                $fields[] = 'introduction';
                foreach ($languageIdList as &$languageId) {
                    $languageData[$languageId]['introduction'] = $info['introduction'];
                }
            }
            if (isset($info['code'])) {
                $fields[] = 'code';
                $languageData['code'] = $info['code'];
            }
            if (isset($info['price'])) {
                $fields[] = 'price';
                $languageData['price'] = $info['price'];
            }
            if (isset($info['importPrice'])) {
                $fields[] = 'importPrice';
                $languageData['importPrice'] = $info['importPrice'];
            }
            if (isset($info['availability'])) {
                $fields[] = 'availability';
                $languageData['availability'] = $info['availability'];
            }
            if (isset($info['deliveryStatus'])) {
                $fields[] = 'deliveryStatus';
                foreach ($languageIdList as &$languageId) {
                    $languageData[$languageId]['deliveryStatus'] = $info['deliveryStatus'];
                }
            }
            if (isset($info['quantity'])) {
                $fields[] = 'quantity';
                $languageData['quantity'] = $info['quantity'];
            }
            if (isset($info['minimumOrder'])) {
                $fields[] = 'minimumOrder';
                $languageData['minimumOrder'] = $info['minimumOrder'];
            }
            if (isset($info['inactive'])) {
                $fields[] = 'inactive';
                $languageData['inactive'] = $info['inactive'];
            }
            if ($this->languageCode) {
                $element->importExternalData($languageData, $fields, [], $languagesManager->getCurrentLanguageId());
            } else {
                $element->importExternalData($languageData, $fields, []);
            }
            $element->persistElementData();

            $info["id"] = $element->id;
            $this->importOriginProductsIndex[$importOrigin][$info['importId']] = $info;

            $result = $element;
        }
        return $result;
    }

    /**
     * Check whether there is a product with such importId and importOrigin. If not, then create a new one
     * @param string $importId
     * @param string $importOrigin
     * @return bool|productElement
     */
    protected function getProductElement($importId, $importOrigin)
    {
        $element = false;
        $structureManager = $this->getService('structureManager');
        if ($catalogueElement = $structureManager->getElementByMarker('catalogue')) {
            if ($id = $this->getElementIdByImportId($importId, $importOrigin)) {
                $element = $structureManager->getElementById($id);
            }
            if (!$element) {
                $element = $structureManager->createElement('product', 'show', $catalogueElement->id);
                $element->prepareActualData();
                $element->persistElementData();
                $this->recordElementImport($element->id, $importId, $importOrigin);
            }
        }
        return $element;
    }

    //todo: check and refactor this functionality
    public function updateProductsStatuses($statusesIndex)
    {
        if (count($statusesIndex) > 0) {
            $transportObject = mysqliTransport::getInstance();
            $transportObject->setResourceName('module_product');

            foreach ($statusesIndex as $productId => $productInfo) {
                $status = $productInfo['availability'];
                $price = $productInfo['price'];
                $quantity = $productInfo['quantity'];
                $vatIncluded = $productInfo['vatIncluded'];

                $transportObject->setSearchLines([
                    'id' => $productId,
                ]);
                $dataLines = [
                    'availability' => $status,
                    'price' => $price,
                    'quantity' => $quantity,
                    'inactive' => '0',
                    'vatIncluded' => $vatIncluded,
                ];
                if (isset($productInfo['importPrice'])) {
                    $dataLines['importPrice'] = $productInfo['importPrice'];
                }
                $transportObject->setDataLines($dataLines);
                $transportObject->updateData();
            }
        }
    }

    public function disableProducts($idList)
    {
        $productsIds = [];
        if (count($idList) > 0) {
            $productsIds = $this->importIdsToElementsIds($idList, $this->importOrigin);
        }
        if ($productsIds) {
            $transportObject = mysqliTransport::getInstance();
            $transportObject->setResourceName('module_product');
            $transportObject->setSearchLines(['id' => $productsIds]);
            $transportObject->setDataLines([
                'availability' => 'unavailable',
                'inactive' => '1',
            ]);
            $transportObject->updateData();
        }
    }

    public function importBrandInfo($info)
    {
        $result = false;
        if (($brandsIndex = $this->getBrandsIndex()) !== false) {
            $title = trim(mb_strtolower($info['title']));
            if (isset($brandsIndex[$title])) {
                $result = $brandsIndex[$title];
            } else {
                $result = $this->createNewBrand($info);
            }
        }
        return $result;
    }

    protected function getBrandsIndex()
    {
        if (is_null($this->brandsIndex)) {
            $this->brandsIndex = [];

            $structureManager = $this->getService('structureManager');
            if ($brandsElement = $structureManager->getElementByMarker('brands')) {
                $brandsList = $structureManager->getElementsChildren($brandsElement->id);
                foreach ($brandsList as &$brand) {
                    $title = trim(mb_strtolower(html_entity_decode($brand->title, ENT_QUOTES)));
                    $this->brandsIndex[$title] = $brand;
                }
            }
        }
        return $this->brandsIndex;
    }

    protected function createNewBrand(&$info)
    {
        $result = false;
        $structureManager = $this->getService('structureManager');
        if ($brandsElement = $structureManager->getElementByMarker('brands')) {
            $element = $structureManager->createElement('brand', 'show', $brandsElement->id);
            $element->prepareActualData();

            $element->title = $info['title'];
            $element->structureName = $info['title'];
            $element->persistElementData();
            $element->connectWithAutomaticBrandsLists();
            $this->recordElementImport($element->id, $info['importId'], $info['importOrigin']);

            $title = trim(mb_strtolower(html_entity_decode($element->title, ENT_QUOTES)));
            $this->brandsIndex[$title] = $element;

            $result = $element;
        }
        return $result;
    }

    public function checkBrandLink($brandInfo, $productImportId, $importOrigin)
    {
        if (($brandsIndex = $this->getBrandsIndex()) !== false) {
            $title = trim(mb_strtolower($brandInfo['title']));
            if (isset($brandsIndex[$title])) {
                $brandElement = $brandsIndex[$title];
                $existingProductsIndex = $this->getExistingProductsIndex($importOrigin);
                if ($productId = $existingProductsIndex[$productImportId]['id']) {
                    $linksManager = $this->getService('linksManager');
                    $linksManager->linkElements($brandElement->id, $productId, 'productbrand');
                }
            }
        }
    }

    public function getImportCategoriesInfo()
    {
        if ($this->importCategoriesInfo === null) {
            $languagesManager = $this->getService('languagesManager');
            $languageId = $languagesManager->getCurrentLanguageId();

            $importedElementsIds = $this->getAllImportElementsIds();
            $categoriesList = [];
            $collection = persistableCollection::getInstance('module_category');
            $conditions = [
                [
                    'id',
                    'IN',
                    $importedElementsIds,
                ],
                [
                    'languageId',
                    '=',
                    $languageId,
                ],
            ];

            $importCategoriesIds = [];
            if ($records = $collection->conditionalLoad([
                'id',
            ], $conditions)
            ) {
                foreach ($records as &$record) {
                    $importCategoriesIds[] = $record['id'];
                }
            }
            if ($importCategoriesIds) {
                $conditions = [
                    [
                        'elementId',
                        'IN',
                        $importCategoriesIds,
                    ],
                ];
                $records = $this->originsCollection->conditionalLoad(
                    ['DISTINCT elementId, importOrigin, importId'],
                    $conditions, [], [], [], true);
                if ($records) {
                    foreach ($records as &$record) {
                        $origin = $record['importOrigin'];
                        $importId = $record['importId'];
                        $elementId = $record['elementId'];
                        if (!isset($categoriesList[$origin])) {
                            $categoriesList[$origin] = [];
                        }
                        if (!isset($categoriesList[$origin][$elementId])) {
                            $categoriesList[$origin][$elementId] = [];
                        }
                        $categoriesList[$origin][$elementId][] = $importId;
                    }
                }
            }
            $this->importCategoriesInfo = $categoriesList;
        }
        return $this->importCategoriesInfo;
    }

    public function getCategoryElementIdByImportId($importId, $origin)
    {
        $categoriesInfo = $this->getCategoriesInfoByOrigin($origin);
        foreach ($categoriesInfo as $elementId => $importIds) {
            foreach ($importIds as $elementImportId) {
                if ($importId == $elementImportId) {
                    return $elementId;
                }
            }
        }
        return 0;
    }

    public function getCategoryElementIdByTitle($categoryTitle, $parentCategoryId = null)
    {
        $languagesManager = $this->getService('languagesManager');
        $languageId = $languagesManager->getCurrentLanguageId();

        $db = $this->getService('db');
        $query = $db->table('module_category')
            ->select('id')
            ->where('title', '=', $categoryTitle)
            ->where('languageId', '=', $languageId)
            ->limit(1);
        if ($parentCategoryId) {
            $query->whereIn('id', function ($subQuery) use ($parentCategoryId) {
                $subQuery->select('childStructureId')
                    ->from('structure_links')
                    ->where('parentStructureId', '=', $parentCategoryId)
                    ->where('type', '=', 'structure');
            });
        }
        if ($record = $query->first()) {
            if ($categoryElement = $this->getService('structureManager')->getElementById($record['id'])) {
                return $categoryElement->id;
            }
        }
        return 0;
    }

    public function getCategoriesInfoByOrigin($origin)
    {
        $originCategoryInfo = [];
        $categoriesInfo = $this->getImportCategoriesInfo();
        if (isset($categoriesInfo[$origin])) {
            $originCategoryInfo = $categoriesInfo[$origin];
        }
        return $originCategoryInfo;
    }

    public function setImportOrigin($newOrigin)
    {
        $this->importOrigin = $newOrigin;
    }

    public function getProductParameterGroups($productImportId, $importOrigin)
    {
        $result = null;
        if ($product = $this->getProductByImportId($productImportId, $importOrigin)) {
            $result = $product->getParametersGroups();
        }
        return $result;
    }

    public function productHasParameters($productImportId, $importOrigin)
    {
        $result = false;
        if ($productElementId = $this->getExistingProductElementId($productImportId, $importOrigin)) {
            $collection = persistableCollection::getInstance('module_product_parameter_value');
            if ($collection->conditionalLoad(['id'], [
                [
                    'productId',
                    '=',
                    $productElementId,
                ],
            ], [], [
                0,
                1,
            ])
            ) {
                $result = true;
            }
        }
        return $result;
    }

    public function getProductImages($productImportId, $importOrigin)
    {
        $result = null;
        if ($product = $this->getProductByImportId($productImportId, $importOrigin)) {
            $result = $product->getImagesList();
        }
        return $result;
    }

    public function productHasImages($productImportId, $importOrigin)
    {
        $result = false;
        if ($productElementId = $this->getExistingProductElementId($productImportId, $importOrigin)) {
            if ($links = $this->getService('linksManager')
                ->getElementsLinks($productElementId, 'structure', 'parent')
            ) {
                foreach ($links as &$link) {
                    if (($element = $this->getService('structureManager')
                            ->getElementById($link->childStructureId)) && $element->structureType == 'galleryImage'
                    ) {
                        $result = true;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    public function getProductBrand($productImportId, $importOrigin)
    {
        $result = null;
        if ($product = $this->getProductByImportId($productImportId, $importOrigin)) {
            $result = $product->getBrandElement();
        }
        return $result;
    }

    public function productHasBrand($productImportId, $importOrigin)
    {
        $result = false;
        if ($productElementId = $this->getExistingProductElementId($productImportId, $importOrigin)) {
            if ($this->getService('linksManager')->getConnectedIdList($productElementId, 'productbrand', 'child')) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @param string $productImportId
     * @param string $importOrigin
     * @return bool|productElement
     */
    protected function getProductByImportId($productImportId, $importOrigin)
    {
        $product = null;
        $existingProductsIndex = $this->getExistingProductsIndex($importOrigin);
        if (isset($existingProductsIndex[$productImportId])) {
            $product = $this->getService('structureManager')
                ->getElementById($existingProductsIndex[$productImportId]["id"]);
        }
        return $product;
    }

    protected function getExistingProductElementId($productImportId, $importOrigin)
    {
        $id = null;
        $existingProductsIndex = $this->getExistingProductsIndex($importOrigin);
        if (isset($existingProductsIndex[$productImportId])) {
            $id = $existingProductsIndex[$productImportId]["id"];
        }
        return $id;
    }

    public function ensureParametersGroupExistence($importCode)
    {
        $structureManager = $this->getService('structureManager');
        if ($parametersMenuElement = $structureManager->getElementByMarker('productparameters')) {
            $groupsList = $structureManager->getElementsChildren($parametersMenuElement->id);
            $found = false;
            foreach ($groupsList as &$group) {
                if ($group->importCode == 'migration') { // TODO: what's going on here?
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $element = $structureManager->createElement('productParametersGroup', 'show', $parametersMenuElement->id);
                $element->prepareActualData();
                $element->title = $importCode;
                $element->structureName = $importCode;

                $element->importCode = $importCode;
                $element->persistElementData();
            }
        }
    }

    public function getElementImportOriginInfo($elementId)
    {
        return $this->originsCollection->conditionalLoad('id', [
            [
                'elementId',
                '=',
                $elementId,
            ],
        ]);
    }

    public function getAllImportElementsIdsByOrigin($importOrigin)
    {
        $elementsIds = [];
        $rows = $this->originsCollection->conditionalLoad('elementId', [
            [
                'importOrigin',
                '=',
                $importOrigin,
            ],
        ]);
        foreach ($rows as &$row) {
            $elementsIds[] = $row['elementId'];
        }
        return array_unique($elementsIds);
    }

    public function getAllImportElementsIds()
    {
        $elementsIds = [];
        $rows = $this->originsCollection->conditionalLoad('elementId', [
            [
                'importOrigin',
                '!=',
                '',
            ],
        ]);
        foreach ($rows as &$row) {
            $elementsIds[] = $row['elementId'];
        }
        return $elementsIds;
    }

    public function getElementImportId($elementId, $importOrigin)
    {
        $importIndex = $this->getElementImportIdIndex($importOrigin);
        return isset($importIndex[$elementId]) ? $importIndex[$elementId] : 0;
    }

    protected function getElementImportIdIndex($importOrigin)
    {
        if ($this->elementImportIdIndex === null) {
            $this->elementImportIdIndex = [];
        }
        if (!isset($this->elementImportIdIndex[$importOrigin])) {
            $this->elementImportIdIndex[$importOrigin] = [];
            $rows = $this->originsCollection->conditionalLoad(['elementId', 'importId'], [
                [
                    'importOrigin',
                    '=',
                    $importOrigin,
                ],
            ]);
            $index = &$this->elementImportIdIndex[$importOrigin];
            foreach ($rows as &$row) {
                $index[$row['elementId']] = $row['importId'];
            }
        }
        return $this->elementImportIdIndex[$importOrigin];
    }

    public function getElementIdByImportId($importId, $origin)
    {
        $elementId = 0;
        $conditions = [
            [
                'importOrigin',
                '=',
                $origin,
            ],
            [
                'importId',
                '=',
                $importId,
            ],
        ];
        $records = $this->originsCollection->conditionalLoad(['elementId'], $conditions);
        if ($records) {
            $elementId = $records[0]['elementId'];
        }
        return $elementId;
    }

    public function getElementsIdsByImportId($importId, $origin, $filterIds = [])
    {
        $elementIds = [];
        $conditions = [
            [
                'importOrigin',
                '=',
                $origin,
            ],
            [
                'importId',
                '=',
                $importId,
            ],
        ];
        if ($filterIds) {
            $conditions[] = [
                'elementId',
                'IN',
                $filterIds,
            ];
        }
        $records = $this->originsCollection->conditionalLoad(['elementId'], $conditions, ['id' => 'desc']);
        foreach ($records as &$record) {
            $elementIds[] = $record['elementId'];
        }
        return $elementIds;
    }

    public function importIdsToElementsIds($importIds, $importOrigin)
    {
        $elementsIds = [];
        $conditions = [
            [
                'importOrigin',
                '=',
                $importOrigin,
            ],
            [
                'importId',
                'IN',
                $importIds,
            ],
        ];
        $records = $this->originsCollection->conditionalLoad(['elementId'], $conditions);
        if ($records) {
            foreach ($records as &$record) {
                $elementsIds[] = $record['elementId'];
            }
        }
        return $elementsIds;
    }

    public function elementsIdsToImportIds($elementIds, $importOrigin)
    {
        $importIds = [];
        $conditions = [
            [
                'importOrigin',
                '=',
                $importOrigin,
            ],
            [
                'elementId',
                'IN',
                $elementIds,
            ],
        ];
        $records = $this->originsCollection->conditionalLoad(['importId'], $conditions);
        if ($records) {
            foreach ($records as &$record) {
                $importIds[] = $record['importId'];
            }
        }
        return $importIds;
    }

    public function recordElementImport($elementId, $importId, $importOrigin)
    {
        $this->getElementImportIdIndex($importOrigin);
        if (!isset($this->elementImportIdIndex[$importOrigin][$elementId])) {
            $fieldObject = $this->originsCollection->getEmptyObject();
            $fieldObject->elementId = $elementId;
            $fieldObject->importId = $importId;
            $fieldObject->importOrigin = $importOrigin;
            $fieldObject->persist();
        }
        $this->elementImportIdIndex[$importOrigin][$elementId] = $importId;
    }

    public function getAdjustedProductPrice(
        $productInfo,
        $categoryPriceModifier,
        $priceClassMargins = [],
        $importOrigin,
        $recommendedPrice = 0
    ) {
        $originalPrice = $productInfo['price'];
        $productId = $productInfo['id'];
        $modifier = 0;

        if ($matchingRule = $this->getMatchingRule($productId, $importOrigin, $originalPrice)) {
            if ($matchingRule->action == 'modify') {
                $modifier = str_replace('%', '', $matchingRule->priceModifier);
                $adjustedPrice = $originalPrice + $originalPrice / 100 * $modifier;
            } else {
                if ($recommendedPrice > 0) {
                    $adjustedPrice = $recommendedPrice;
                } else {
                    $adjustedPrice = $originalPrice;
                }
            }
        } else {
            if ($priceClassMargins) {
                foreach ($priceClassMargins as &$marginInfo) {
                    if ($originalPrice >= $marginInfo['fromPrice'] && $originalPrice < $marginInfo['toPrice']) {
                        $modifier = str_replace('%', '', $marginInfo['priceModifier']);
                        break;
                    }
                }
            }
            if ($modifier == 0 && $categoryPriceModifier != '') {
                $modifier = str_replace('%', '', $categoryPriceModifier);
            }
            $adjustedPrice = $originalPrice + $originalPrice / 100 * $modifier;
        }
        return $adjustedPrice;
    }

    public function getMatchingRule($productId, $importOrigin, $originalPrice)
    {
        $matchingRule = null;
        $rules = $this->getCalculationRulesElements();
        foreach ($rules as &$rule) {
            if ($rule->matchProduct($productId, $originalPrice, $importOrigin)) {
                $matchingRule = $rule;
                break;
            }
        }
        return $matchingRule;
    }

    protected function getCalculationRulesElements()
    {
        if ($this->rulesElements === null) {
            $this->rulesElements = $this->getService('structureManager')->getElementsByType('importCalculationsRule');
            $times = [];
            foreach ($this->rulesElements as &$ruleElement) {
                $times[] = strtotime($ruleElement->dateModified);
            }
            array_multisort($times, SORT_DESC, $this->rulesElements);
        }
        return $this->rulesElements;
    }

    protected function loadProductImportCategoriesInfo($warehouseCode)
    {
        $this->productImportCategoriesInfo = [$warehouseCode => []];
        $index = &$this->productImportCategoriesInfo[$warehouseCode];
        $collection = persistableCollection::getInstance('product_import_categories');
        $records = $collection->conditionalLoad(['productId', 'warehouseCategoryCode', 'warehouseCategoryTitle'],
            [
                [
                    'warehouseCode',
                    '=',
                    $warehouseCode,
                ],
            ]);
        if ($records) {
            foreach ($records as $record) {
                extract($record, EXTR_OVERWRITE);
                if (!isset($index[$productId])) {
                    $index[$productId] = [];
                }
                $index[$productId][$warehouseCategoryCode] = $warehouseCategoryTitle;
            }
        }
    }

    public function saveProductImportCategoryInfo(
        $productId,
        $warehouseCode,
        $warehouseCategoryCode,
        $warehouseCategoryTitle
    ) {
        if (!isset($this->productImportCategoriesInfo[$warehouseCode])) {
            $this->loadProductImportCategoriesInfo($warehouseCode);
        }
        $warehouseRecords = &$this->productImportCategoriesInfo[$warehouseCode];
        if (!isset($warehouseRecords[$productId]) || !isset($warehouseRecords[$productId][$warehouseCategoryCode])) {
            $collection = persistableCollection::getInstance('product_import_categories');
            $fieldObject = $collection->getEmptyObject();
            $fieldObject->productId = $productId;
            $fieldObject->warehouseCode = $warehouseCode;
            $fieldObject->warehouseCategoryCode = $warehouseCategoryCode;
            $fieldObject->warehouseCategoryTitle = $warehouseCategoryTitle;
            $fieldObject->persist();
            if (!isset($warehouseRecords[$productId])) {
                $warehouseRecords[$productId] = [];
            }
            $warehouseRecords[$productId][$warehouseCategoryCode] = $warehouseCategoryTitle;
        } elseif ($warehouseRecords[$productId][$warehouseCategoryCode] != $warehouseCategoryTitle) {
            $transportObject = mysqliTransport::getInstance();
            $transportObject->setResourceName('product_import_categories');
            $transportObject->setSearchLines([
                'productId' => $productId,
                'warehouseCode' => $warehouseCode,
                'warehouseCategoryCode' => $warehouseCategoryCode,
            ]);
            $transportObject->setDataLines([
                'warehouseCategoryTitle' => $warehouseCategoryTitle,
            ]);
            $transportObject->updateData();
            $warehouseRecords[$productId][$warehouseCategoryCode] = $warehouseCategoryTitle;
        }
    }
}
