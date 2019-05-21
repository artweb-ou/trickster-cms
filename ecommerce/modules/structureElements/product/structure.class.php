<?php

/**
 * Class productElement
 *
 * @property string $title
 * @property string $description
 * @property string $code
 * @property integer $inactive
 * @property integer $showincategory
 * @property integer $vatIncluded
 * @property string $introduction
 * @property string $content
 * @property string $color
 * @property string $deliveryStatus
 * @property float $price
 * @property float $oldPrice
 * @property string $importPrice
 * @property integer $purchaseCount
 * @property integer $lastPurchaseDate
 * @property integer $brandId
 * @property integer[] $categories
 * @property integer[] $products
 * @property integer[] $products2
 * @property integer[] $products3
 * @property integer[] $connectedProductCategories
 * @property integer $qtFromConnectedCategories
 * @property array $discounts
 * @property array $formParameters
 * @property array $formDeliveries
 * @property string $deliveryPriceType
 * @property string $metaTitle
 * @property string $h1
 * @property string $metaDescription
 * @property string $canonicalUrl
 * @property integer $metaDenyIndex
 * @property array $elements
 * @property string $availability
 * @property integer $minimumOrder
 * @property array $optionsPricingInput
 * @property array $optionsImagesInput
 * @property integer $quantity
 * @property string $comment_author
 * @property string $comment_email
 * @property string $comment_content
 * @property string $comment_captcha
 * @property array $importInfo
 * @property string $unit
 */
class productElement extends structureElement implements
    MetadataProviderInterface,
    LdJsonProviderInterface,
    OpenGraphDataProviderInterface,
    TwitterDataProviderInterface
{
    use deprecatedProductElementTrait, GalleryInfoProviderTrait, FilesElementTrait, ImagesElementTrait;
    use MetadataProviderTrait {
        getTextContent as getTextContentTrait;
    }
    use ConfigurableLayoutsProviderTrait;
    use CommentsTrait;
    use DeliveryPricesTrait;
    use EventLoggingElementTrait;

    use ProductsAvailabilityOptionsTrait;
    use ProductIconLocationOptionsTrait;
    use ProductIconRoleOptionsTrait;
    use ConnectedParametersProviderTrait;

    public $dataResourceName = 'module_product';
    protected $allowedTypes = ['galleryImage'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $connectedProducts;
    protected $connectedProducts2;
    protected $connectedProducts3;
    protected $connectedProductCategories;
    protected $primaryParametersInfo;
    protected $parametersGroupsInfo;
    protected $parametersInfoList;
    protected $selectionsInfoList;
    protected $basketSelectionsInfo;
    /**
     * @var categoryElement
     */
    protected $parentCategory;
    /**
     * @var categoryElement
     */
    protected $requestedParentCategory;
    /**
     * @var categoryElement
     */
    protected $requestedTopCategory;
    protected $deliveryTypesInfo;
    protected $brandElement;
    protected $connectedDiscounts;
    protected $inquryForm;
    protected $imageIds;
    protected $adminIconsList;
    /**
     * @var galleryImageElement
     */
    protected $firstImage;
    protected $calculatedPrice;
    protected $calculatedOldPrice;
    protected $campaignDiscounts;
    protected $deepParentCategories;
    protected $deepParentCategoriesIdList;
    protected $brandsIdList;
    /**
     * @var feedbackElement
     */
    protected $inquiryForm;
    public $image;
    public $originalName;
    protected $xmlSourcesCodeNames;
    protected $nextProduct;
    protected $previousProduct;
    protected $productUnit;
    protected $influentialSelections;
    protected $selectionsPricingMap;

    protected $iconsInfo;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['code'] = 'text';
        $moduleStructure['inactive'] = 'checkbox';
        $moduleStructure['showincategory'] = 'checkbox';
        $moduleStructure['vatIncluded'] = 'checkbox';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['content'] = 'html';
        $moduleStructure['color'] = 'text';
        $moduleStructure['deliveryStatus'] = 'text';
        $moduleStructure['price'] = 'emptyMoney';
        $moduleStructure['oldPrice'] = 'money';
        $moduleStructure['importPrice'] = 'text';
        $moduleStructure['purchaseCount'] = 'text';
        $moduleStructure['lastPurchaseDate'] = 'text';
        $moduleStructure['brandId'] = 'text';

        $moduleStructure['categories'] = 'numbersArray';
        $moduleStructure['products'] = 'numbersArray';
        $moduleStructure['products2'] = 'numbersArray';
        $moduleStructure['products3'] = 'numbersArray';

        $moduleStructure['connectedProductCategories'] = 'numbersArray';
        $moduleStructure['qtFromConnectedCategories'] = 'text';

        $moduleStructure['discounts'] = 'array';
        $moduleStructure['formParameters'] = 'array';
        $moduleStructure['formDeliveries'] = 'array';
        $moduleStructure['formActiveDeliveries'] = 'array';
        $moduleStructure['deliveryPriceType'] = 'text';

        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['elements'] = 'array';

        $moduleStructure['availability'] = 'text';
        $moduleStructure['minimumOrder'] = 'text';
        $moduleStructure['optionsPricingInput'] = 'array';
        $moduleStructure['optionsImagesInput'] = 'array';
        // availability types:
        //  available
        //  quantity_dependent
        //  inquirable
        //  unavailable
        //  available_inquirable
        $moduleStructure['quantity'] = 'text';
        // misc tmp
        $moduleStructure['importInfo'] = 'array';

        $moduleStructure['unit'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'content';
        $multiLanguageFields[] = 'introduction';
        $multiLanguageFields[] = 'deliveryStatus';
        $multiLanguageFields[] = 'metaTitle';
        $multiLanguageFields[] = 'h1';
        $multiLanguageFields[] = 'metaDescription';
        $multiLanguageFields[] = 'unit';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showTexts',
            'showParameters',
            'showOptionsPricingForm',
            'showOptionsImagesForm',
            'showSeoForm',
            'showDelivery',
            'showImages',
            'showFiles',
            'showIconForm',
            'showImportForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getParametersHintsInfo()
    {
        $info = [];
        if ($parameterGroups = $this->getParametersGroups()) {
            foreach ($parameterGroups as &$parameterGroup) {
                if ($parametersList = $parameterGroup->getParametersList()) {
                    foreach ($parametersList as &$parameter) {
                        if ($parameter->hint) {
                            if (!isset($info[$parameter->id])) {
                                $info[$parameter->id] = [];
                            }
                            $info[$parameter->id][] = $parameter->hint;
                        }
                        if ($parameter instanceof productSelectionElement) {
                            if ($optionsHints = $parameter->getOptionsHints()) {
                                if (!isset($info[$parameter->id])) {
                                    $info[$parameter->id] = [];
                                }
                                $info[$parameter->id] = array_merge($info[$parameter->id], $optionsHints);
                            }
                        }
                    }
                }
            }
        }
        return $info;
    }

    /**
     * @param bool $formatted
     * @param bool $originalCurrency
     * @return float
     */
    public function getOldPrice($formatted = true, $originalCurrency = false)
    {
        $oldPrice = false;
        if ($this->getPrice() < $this->calculatedOldPrice) {
            if ($originalCurrency) {
                $oldPrice = $this->calculatedOldPrice;
            } else {
                $currencySelector = $this->getService('CurrencySelector');
                $oldPrice = $currencySelector->convertPrice($this->calculatedOldPrice);
            }
            if ($formatted) {
                $oldPrice = sprintf('%01.2f', $oldPrice);
            }
        }

        return $oldPrice;
    }

    public function getBrandsIdList()
    {
        if ($this->brandsIdList === null) {
            $this->brandsIdList = $this->getService('linksManager')
                ->getConnectedIdList($this->id, 'productbrand', 'child');
        }
        return $this->brandsIdList;
    }

    public function setBrandsIdList($brandsIds)
    {
        $this->brandsIdList = $brandsIds;
    }

    /**
     * @param bool $formatted
     * @param bool $originalCurrency
     * @param string $format
     * @param bool $includeVat
     * @return float
     */
    public function getPrice($formatted = true, $originalCurrency = false, $format = '%01.2f', $includeVat = true)
    {
        if ($this->calculatedPrice === null) {
            $discountsManager = $this->getService('shoppingBasketDiscounts');
            $this->calculatedPrice = $this->price;
            $this->calculatedOldPrice = $this->oldPrice;
            $mainConfig = $this->getService('ConfigManager')->getConfig('main');
            $vatRateSetting = $mainConfig->get('vatRate');
            $vatIncluded = $this->vatIncluded || $mainConfig->get('pricesContainVat') === true;
            if (!$vatIncluded) {
                $this->calculatedPrice *= $vatRateSetting;
                $this->calculatedOldPrice *= $vatRateSetting;
            }
            $discountAmount = $discountsManager->getProductDiscount($this->id, $this->calculatedPrice);
            if ($discountAmount) {
                //display full price as "old price" if there is no bigger manually set value
                if ($this->calculatedOldPrice <= $this->calculatedPrice) {
                    $this->calculatedOldPrice = $this->calculatedPrice;
                }
                $this->calculatedPrice -= $discountAmount;
            }
        }
        $price = $this->calculatedPrice;
        if (!$includeVat) {
            $mainConfig = $this->getService('ConfigManager')->getConfig('main');
            $vatRateSetting = $mainConfig->get('vatRate');

            $price /= $vatRateSetting;
        }
        if (!$originalCurrency) {
            $currencySelector = $this->getService('CurrencySelector');
            $price = $currencySelector->convertPrice($price);
        }
        if ($formatted) {
            $price = sprintf($format, $price);
        }
        return $price;
    }

    /**
     * @param bool $formatted
     * @param bool $originalCurrency
     * @return float
     */
    public function getVatFreePrice($formatted = true, $originalCurrency = false)
    {
        $vatFreePrice = $this->getPrice(false, true, '%01.2f', false);

        if (!$originalCurrency) {
            $currencySelector = $this->getService('CurrencySelector');
            $vatFreePrice = $currencySelector->convertPrice($vatFreePrice);
        }
        if ($formatted) {
            $vatFreePrice = sprintf('%01.2f', $vatFreePrice);
        }
        return $vatFreePrice;
    }

    /**
     * @param bool $formatted
     * @param bool $originalCurrency
     * @return float
     */
    public function getVat($formatted = true, $originalCurrency = false)
    {
        $price = $this->getPrice(false, true);
        $vatRate = $this->getService('ConfigManager')->get('main.vatRate');
        $vat = $price - $price / $vatRate;
        if (!$originalCurrency) {
            $currencySelector = $this->getService('CurrencySelector');
            $vat = $currencySelector->convertPrice($vat);
        }
        if ($formatted) {
            $vat = sprintf('%01.2f', $vat);
        }

        return $vat;
    }

    /**
     * @param bool $formatted
     * @param bool $originalCurrency
     * @return float
     */
    public function getVatFreeOldPrice($formatted = true, $originalCurrency = false)
    {
        $vatRate = $this->getService('ConfigManager')->get('main.vatRate');
        $vatFreeOldPrice = $this->getOldPrice(false, true) / $vatRate;
        if (!$originalCurrency) {
            $currencySelector = $this->getService('CurrencySelector');
            $vatFreeOldPrice = $currencySelector->convertPrice($vatFreeOldPrice);
        }
        if ($formatted) {
            $vatFreeOldPrice = sprintf('%01.2f', $vatFreeOldPrice);
        }

        return $vatFreeOldPrice;
    }

    /**
     * @param bool $formatted - return float if false, rounded string if true
     * @param string $format - sprintf format string
     * @return float
     */
    public function getDiscountPercent($formatted = true, $format = "%01.2f")
    {
        $percent = false;
        if ($oldPrice = $this->getOldPrice(false, true)) {
            $percent = 100 * ($this->getDiscountAmount(false, true) / $oldPrice);
            if ($formatted) {
                $percent = round($percent * 20) / 20;
                $percent = sprintf($format, $percent);
            }
        }
        return $percent;
    }

    /**
     * @param bool $formatted
     * @param bool $originalCurrency
     * @return float
     */
    public function getDiscountAmount($formatted = true, $originalCurrency = false)
    {
        $amount = $this->getOldPrice(false, true) - $this->getPrice(false, true);
        if (!$originalCurrency) {
            $currencySelector = $this->getService('CurrencySelector');
            $amount = $currencySelector->convertPrice($amount);
        }
        if ($formatted) {
            $amount = sprintf('%01.2f', $amount);
        }
        return $amount;
    }

    public function isPurchasable($purchaseQuantity = 1)
    {
        //put magic property "availability" to variable to get extra speed on this method when repeated for 1000 products.
        $availability = $this->availability;
        return !($availability == "inquirable" || $availability == "unavailable" || ($availability == "quantity_dependent" && $purchaseQuantity > (int)$this->quantity));
    }

    public function getTextContent()
    {
        if (is_null($this->textContent)) {
            $this->textContent = $this->getTextContentTrait();

            //brand and parameters should be loaded only if the element is final
            if ($this->final) {
                if ($categoryMetaDescriptionTemplate = $this->getCategoryMetaDescriptionTemplate()) {
                    $this->textContent = $this->populateSeoTemplate($categoryMetaDescriptionTemplate);
                } else {
                    if ($brandElement = $this->getBrandElement()) {
                        $this->textContent .= " " . $brandElement->title;
                    }

                    if ($parameterGroups = $this->getParametersGroups()) {
                        $parameterString = "";
                        foreach ($parameterGroups as &$parameterGroup) {
                            foreach ($parameterGroup->getParametersList() as $parameter) {
                                $parameterString = $parameter->title;
                                if ($parameter->structureType == 'productParameter') {
                                    $parameterString .= ": " . $parameter->value;
                                } elseif ($parameter->structureType == 'productSelection') {
                                    $parameterString .= " ";
                                    foreach ($parameter->productOptions as &$productOption) {
                                        $parameterString = $parameterString ? $parameterString . ", " . $productOption->title : $parameterString . ": " . $productOption->title;
                                    }
                                }
                            }
                        }
                        $this->textContent .= " " . $parameterString;
                    }
                }
            }
        }
        return $this->textContent;
    }

    protected function populateSeoTemplate($template)
    {
        $translationsManager = $this->getService('translationsManager');

        preg_match_all("|{(.*)}|sUi", $template, $results);
        $search = [];
        $replace = [];
        foreach ($results[1] as $result) {
            $search[] = '{' . $result . '}';
            switch ($result) {
                case 'title':
                    {
                        $replace[] = $this->title;
                        break;
                    }
                case 'category':
                    {
                        if ($category = $this->getRequestedParentCategory()) {
                            $replace[] = $category->title;
                        }
                        break;
                    }
                case 'topCategory':
                    {
                        if ($category = $this->getRequestedTopCategory()) {
                            $replace[] = $category->title;
                        }
                        break;
                    }
                case 'brand':
                    {
                        $brand = $this->getBrandElement();
                        $replace[] = $brand->title;
                        break;
                    }
                case 'price':
                    {
                        $replace[] = $this->getPrice();
                        break;
                    }
                case 'availability':
                    {
                        $replace[] = $translationsManager->getTranslationByName('product.' . $this->availability);
                        break;
                    }
                case 'deliveryStatus':
                    {
                        $replace[] = $this->getDeliveryStatus();
                        break;
                    }
                case (stripos($result, 'parameterValue:') !== false):
                    {
                        $id = (int)substr($result, 15);
                        if ($value = $this->getParameterValueById($id)) {
                            $replace[] = mb_strtolower($value);
                        } else {
                            $replace[] = '';
                        }
                        break;
                    }
                default:
                    $replace[] = '';
            }
        }

        return str_replace($search, $replace, $template);
    }

    /**
     * returns list of all parent categories
     *
     * @param bool $forceUpdate - ignore cache when set
     * @return categoryElement[]
     */
    public function getConnectedCategories($forceUpdate = false)
    {
        if ($forceUpdate) {
            $this->logError('Deprecated workaround is used in productElement::getConnectedCategories()');
        }
        $structureManager = $this->getService('structureManager');

        $categories = [];
        /**
         * @var structureManager $structureManager
         * @var structureElement $parentsList
         */
        $parentsList = $structureManager->getElementsParents($this->id, $forceUpdate, 'catalogue', false);
        if ($parentsList) {
            foreach ($parentsList as &$parentElement) {
                if ($parentElement->structureType == 'category') {
                    $categories[] = $parentElement;
                }
            }
        }
        return $categories;
    }

    /**
     * @return array|null
     */
    public function getDeepParentCategories()
    {
        $deepCategories = [];
        if ($this->deepParentCategories === null) {
            $this->deepParentCategories = [];
            /**
             * @var structureManager $structureManager
             * @var structureElement $parentsList
             */
            $structureManager = $this->getService('structureManager');
            if ($deepCategories = $this->getConnectedCategories()) {
                foreach ($deepCategories as &$category) {
                    $this->deepParentCategories[] = $category;
                    $parentsList = $structureManager->getElementsParents($category->id, false, '', false);
                    foreach ($parentsList as &$parentsListItem) {
                        if ($parentsListItem->structureType == 'category') {
                            $this->deepParentCategories[] = $parentsListItem;
                        }
                    }
                }
            }
        }
        return $deepCategories + $this->deepParentCategories;
    }


    /**
     * @return array
     */
    public function getConnectedBrands()
    {
        $structureManager = $this->getService('structureManager');

        $brands = [];
        /**
         * @var structureManager $structureManager
         * @var structureElement $parentsList
         */
        $parentsList = $structureManager->getElementsParents($this->id, 'catalogue', false);
        if ($parentsList) {
            foreach ($parentsList as &$parentElement) {
                if ($parentElement->structureType == 'brand') {
                    $brands[] = $parentElement;
                }
            }
        }
        return $brands;
    }

    public function getConnectedCatalogues($forceUpdate = false)
    {
        $structureManager = $this->getService('structureManager');

        $catalogues = [];
        $parentsList = $structureManager->getElementsParents($this->id, $forceUpdate, 'productCatalogueProduct');
        if ($parentsList) {
            foreach ($parentsList as &$parentElement) {
                if ($parentElement instanceof categoryElement) {
                    $catalogues[] = $parentElement;
                }
            }
        }
        return $catalogues;
    }

    public function getRequestedParentCategory()
    {
        if ($this->requestedParentCategory === null) {
            $this->requestedParentCategory = false;
            $structureManager = $this->getService('structureManager');
            if ($parentsList = $structureManager->getElementsParents($this->id)) {
                foreach ($parentsList as &$parentElement) {
                    if ($parentElement->requested) {
                        $this->requestedParentCategory = $parentElement;
                        break;
                    } elseif (!$this->requestedParentCategory) {
                        $this->requestedParentCategory = $parentElement;
                    }
                }
            }
        }
        return $this->requestedParentCategory;
    }

    public function getRequestedTopCategory()
    {
        if ($this->requestedTopCategory === null) {
            $this->requestedTopCategory = false;
            if ($parentCategory = $this->getRequestedParentCategory()) {
                $this->requestedTopCategory = $parentCategory->getMainParentCategory();
            }
        }
        return $this->requestedTopCategory;
    }

    public function getParameterByCode($code)
    {
        $result = false;
        if ($parametersInfoList = $this->getParametersInfoList()) {
            foreach ($parametersInfoList as &$parameter) {
                if ($parameter['code'] == $code) {
                    $result = $parameter;
                    break;
                }
            }
        }

        return $result;
    }

    public function getParametersInfoList()
    {
        if ($this->parametersInfoList === null) {
            $this->getParametersGroupsInfo();
        }
        return $this->parametersInfoList;
    }

    public function getParametersGroupsInfo()
    {
        if ($this->parametersGroupsInfo === null) {
            $this->parametersGroupsInfo = [];

            $groupsParentElements = $this->getDeepParentCategories(); //+$this->getConnectedCatalogues(true) +$this->getDeepParentCategories()
            if (!$groupsParentElements) {
                $groupsParentElements = $this->getConnectedCatalogues(true);
            }

            $groupsList = [];
            $groupsIndex = [];

            $sortingIdList = null;

            //todo: replace with index method on parameters group?
            foreach ($groupsParentElements as &$groupsParentElement) {
                if ($groupsParentElement->final || $sortingIdList === null) {
                    $sortingIdList = $groupsParentElement->getParametersIdList();
                }
                $groupElements = $groupsParentElement->getParametersGroups();
                foreach ($groupElements as &$group) {
                    if (!isset($groupsIndex[$group->id])) {
                        $groupsIndex[$group->id] = $group;
                        $groupsList[] = $group;
                    }
                }
            }
            $sortingIdIndex = [];
            if (is_array($sortingIdList)) {
                $sortingIdIndex = array_flip($sortingIdList);
            }
            $parametersManager = $this->getService('ParametersManager');
            foreach ($groupsList as &$group) {
                $groupParameters = $group->getParametersList();
                if (!$groupParameters) {
                    continue;
                }
                $filteredGroupParameters = [];
                foreach ($groupParameters as &$parameterObject) {
                    $values = $parametersManager->getProductParameterValues($this->id, $parameterObject->id);
                    if (!$values) {
                        continue;
                    }
                    $parameterInfo = [
                        'id' => $parameterObject->id,
                        'title' => $parameterObject->title,
                        'code' => $parameterObject->code,
                        'structureType' => $parameterObject->structureType,
                        'type' => $parameterObject->type,
                        'originalName' => $parameterObject->originalName,
                        'image' => $parameterObject->image,
                        'primary' => $parameterObject->primary,
                        'essential' => $parameterObject->primary,
                        'hasHints' => $parameterObject->hasHints(),
                    ];
                    if ($parameterInfo['structureType'] == 'productParameter') {
                        $parameterInfo['basketOption'] = false;
                        $parameterInfo['value'] = $values[0];

                        $this->parametersInfoList[] = $parameterInfo;
                        $filteredGroupParameters[] = $parameterInfo;
                    } elseif ($parameterInfo['structureType'] == 'productSelection') {
                        $parameterInfo['basketOption'] = $parameterObject->option;
                        $parameterInfo['controlType'] = $parameterObject->controlType;
                        $parameterInfo['influential'] = $parameterObject->influential;
                        $parameterInfo['productOptions'] = [];
                        //todo: use index inside parameterObject instead
                        foreach ($parameterObject->getSelectionOptions() as $selectionValueElement) {
                            if (in_array($selectionValueElement->id, $values)) {
                                $parameterInfo['productOptions'][] = [
                                    'title' => $selectionValueElement->title,
                                    'id' => $selectionValueElement->id,
                                    'originalName' => $selectionValueElement->originalName,
                                    'image' => $selectionValueElement->image,
                                    'value' => $selectionValueElement->value,
                                ];
                            }
                        }
                        if ($parameterInfo['productOptions']) {
                            $this->parametersInfoList[] = $parameterInfo;

                            $filteredGroupParameters[] = $parameterInfo;
                        }
                    }
                }
                // TODO (high priority): refactoring
                if ($filteredGroupParameters) {
                    $sort = [];
                    foreach ($filteredGroupParameters as &$parameter) {
                        if (isset($sortingIdIndex[$parameter['id']])) {
                            $sort[] = $sortingIdIndex[$parameter['id']];
                        } else {
                            $sort[] = 0;
                        }
                    }
                    array_multisort($sort, SORT_ASC, $filteredGroupParameters);

                    $this->parametersGroupsInfo[] = [
                        'title' => $group->title,
                        'id' => $group->id,
                        'isMinimized' => $group->isMinimized,
                        'parametersList' => $filteredGroupParameters,
                    ];
                }
            }
        }
        return $this->parametersGroupsInfo;
    }

    public function getBasketSelectionsInfo()
    {
        if ($this->basketSelectionsInfo === null) {
            /**
             * @var ParametersManager $parametersManager
             */
            $parametersManager = $this->getService('ParametersManager');
            $this->basketSelectionsInfo = $parametersManager->getProductBasketSelectionsInfo($this->id);
        }
        return $this->basketSelectionsInfo;
    }

    public function isBasketSelectionRequired()
    {
        return $this->getBasketSelectionsInfo();
    }

    /**
     * @return bool|structureElement
     */
    public function getBrandElement()
    {
        if ($this->brandElement === null) {
            $this->brandElement = false;
            if ($idList = $this->getBrandsIdList()) {
                $structureManager = $this->getService('structureManager');
                $this->brandElement = $structureManager->getElementById(reset($idList));
            }
        }
        return $this->brandElement;
    }

    public function getInquiryForm()
    {
        if ($this->inquiryForm === null) {
            $structureManager = $this->getService('structureManager');
            if ($inquryFormId = $this->getRequestedParentCategory()->getInheritableProperty('feedbackId')) {
                $this->inquiryForm = $structureManager->getElementById($inquryFormId);
            }

            //check all other possible categories if requested doesn't have one
            if (!$this->inquryForm) {
                foreach ($this->getConnectedCategories() as $category) {
                    if ($inquryFormId = $category->getInheritableProperty('feedbackId')) {
                        if ($this->inquiryForm = $structureManager->getElementById($inquryFormId)) {
                            break;
                        }
                    }
                }
            }
            if ($this->inquiryForm) {
                $this->inquiryForm->setProductId($this->id);
            }
        }
        return $this->inquiryForm;
    }

    public function getParentCategories()
    {
    }

    public function getAllConnectedDiscounts()
    {
        if (is_null($this->connectedDiscounts)) {
            $structureManager = $this->getService('structureManager');
            $this->connectedDiscounts = [];
            $linksManager = $this->getService('linksManager');
            $connectedDiscountIds = $linksManager->getConnectedIdList($this->id, "discountProduct", "child");
            foreach ($connectedDiscountIds as &$connectedDiscountId) {
                if ($discount = $structureManager->getElementById($connectedDiscountId)) {
                    $this->connectedDiscounts[] = $discount;
                }
            }
        }
        return $this->connectedDiscounts;
    }

    public function getCampaignDiscounts()
    {
        if (is_null($this->connectedDiscounts)) {
            $this->connectedDiscounts = [];
            $structureManager = $this->getService('structureManager');
            $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');

            if ($discounts = $shoppingBasketDiscounts->getApplicableDiscountsList()) {
                $zeroAndSmallestDiscountOnProduct = false;
                foreach ($discounts as &$discount) {
                    if ($discount->isUsed()) {
                        if ($discount->groupBehaviour == 'useSmallest' && $discount->productDiscount === '0' && $discount->isProductDiscountable($this->id)) {
                            $zeroAndSmallestDiscountOnProduct = $discount;
                        }
                        if ($discount->isProductDiscountable($this->id)) {
                            if ($discountElement = $structureManager->getElementById($discount->id)) {
                                $this->connectedDiscounts[] = $discountElement;
                            }
                        }
                    }
                }
                if ($zeroAndSmallestDiscountOnProduct) {
                    $this->connectedDiscounts = [$zeroAndSmallestDiscountOnProduct];
                }
            }
        }
        return $this->connectedDiscounts;
    }

    /**
     * @return array|null
     */
    public function getDeepParentCategoriesIdList()
    {
        if ($this->deepParentCategoriesIdList === null) {
            $this->deepParentCategoriesIdList = [];
            if ($categories = $this->getConnectedCategories()) {
                foreach ($categories as &$category) {
                    $this->deepParentCategoriesIdList[] = $category->id;

                    $levelCategory = $category;
                    while (method_exists($levelCategory, 'getParentCategory') && ($parentElement = $levelCategory->getParentCategory())) {
                        $this->deepParentCategoriesIdList[] = $parentElement->id;
                        $levelCategory = $parentElement;
                    }
                }
            }
        }
        return $this->deepParentCategoriesIdList;
    }

    //todo: update this to work with admin page
    public function isDiscountProduct()
    {
        $result = false;
        if ($this->getCampaignDiscounts()) {
            $result = true;
        }
        return $result;
    }

    public function getFirstImageElement()
    {
        if (is_null($this->firstImage)) {
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');

            $this->firstImage = false;

            if ($links = $linksManager->getElementsLinks($this->id, "structure", "parent")) {
                foreach ($links as &$link) {
                    if (($element = $structureManager->getElementById($link->childStructureId)) && $element->structureType == 'galleryImage') {
                        $this->firstImage = $element;
                        break;
                    }
                }
            }
        }
        return $this->firstImage;
    }

    public function getFirstImageUrl()
    {
        if (empty($this->firstImage)) {
            $this->getFirstImageElement();
        }
        if (!empty($this->firstImage)) {
            return $this->firstImage->getImageUrl();
        }
        return false;
    }

    /**
     * @return galleryImageElement method for admin panel's icons list
     */
    public function getAdminIconsList()
    {
        if ($this->adminIconsList === null) {
            /**
             * @var ProductIconsManager $productIconsManager
             */
            $productIconsManager = $this->getService('ProductIconsManager');
            $this->adminIconsList = $productIconsManager->getOwnIcons($this->id, $this->structureType);
        }
        return $this->adminIconsList;
    }

    /**
     * @return genericIconElement[]
     *
     * @deprecated
     */
    public function getIconsCompleteList()
    {
        $this->logError('deprecated method getIconsCompleteList used');
        if ($this->iconsCompleteList === null) {
            /**
             * @var ProductIconsManager $productIconsManager
             */
            $productIconsManager = $this->getService('ProductIconsManager');
            $this->iconsCompleteList = $productIconsManager->getProductIcons($this);
        }

        return $this->iconsCompleteList;
    }

    public function getIconsInfo()
    {
        if ($this->iconsInfo == null) {
            $cache = $this->getService('Cache');
            if (($this->iconsInfo = $cache->get($this->id . ':icons') === false)) {
                $this->iconsInfo = [];
                /**
                 * @var ProductIconsManager $productIconsManager
                 */
                $productIconsManager = $this->getService('ProductIconsManager');
                if ($icons = $productIconsManager->getProductIcons($this)) {
                    foreach ($icons as $icon) {
                        $iconInfo = [
                            'title' => $icon->title,
                            'image' => $icon->image,
                            'width' => $icon->iconWidth,
                            'fileName' => $icon->originalName,
                            'iconLocation' => $this->productIconLocationTypes[0],
                        ];
                        if ($icon->structureType == 'genericIcon') {
                            if ($icon->iconLocation) {
                                $iconInfo['iconLocation'] = $this->productIconLocationTypes[$icon->iconLocation];
                            }
                            $iconInfo['iconRole'] = $this->getProductIconRoleType($icon->iconRole);
                            $iconInfo['iconBgColor'] = $icon->iconBgColor;
                            $iconInfo['iconTextColor'] = $icon->iconTextColor;

                            if (!$iconInfo['title'] && ($icon->getProductIconRoleType($icon->iconRole) == 'role_general_discount')) {
                                $iconInfo['title'] = '-' . $this->getDiscountPercent() . '%';
                            }
                        }
                        $this->iconsInfo[] = $iconInfo;
                    }
                }
                if ($discounts = $this->getCampaignDiscounts()) {
                    foreach ($discounts as $discount) {
                        //only show discount with icon applied
                        if (!empty($discount->icon)) {
                            $this->iconsInfo[] = [
                                'title' => $discount->title,
                                'image' => $discount->icon,
                                'width' => $discount->iconWidth,
                                'fileName' => $discount->iconOriginalName,
                                'iconLocation' => $this->productIconLocationTypes[0],
                            ];
                        }
                    }
                }
                $cache->set($this->id . ':icons', $this->iconsInfo, 3600);
            }
        }
        return $this->iconsInfo;
    }


    public function deleteElementData()
    {
        // delete related parameters
        $collection = persistableCollection::getInstance('module_product_parameter_value');
        $searchFields = ['productId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        $pricesManager = $this->getService('ProductOptionsPricesManager');
        $pricesManager->deleteExisting($this->id);
        $productOptionsImagesManager = $this->getService('ProductOptionsImagesManager');
        $productOptionsImagesManager->deleteExisting($this->id);
        $collection = persistableCollection::getInstance('import_origin');
        $searchFields = ['elementId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        $collection = persistableCollection::getInstance('product_import_categories');
        $searchFields = ['productId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        parent::deleteElementData();
    }

    /**
     * Generates a list of parameter values
     * @return null
     *
     * @deprecated - rewrite if required
     */
    protected function getParameterValuesIdIndex()
    {
        if (is_null($this->parameterValuesIdIndex)) {
            $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
            $searchFields = ['productId' => $this->id];
            $valuesIndex = $valuesCollection->load($searchFields, [], 'parameterId');

            $parameterGroups = $this->getParametersGroups();
            foreach ($parameterGroups as &$parameterGroup) {
                foreach ($parameterGroup->getParametersList() as $parameter) {
                    $value = "";
                    if ($parameter->structureType == "productParameter") {
                        $value = $parameter->value;
                    } elseif ($parameter->structureType == "productSelection") {
                        $value = "";
                        $values = $parameter->getSelectionOptions();
                        foreach ($values as &$option) {
                            if ($value) {
                                $value .= ", " . $option->title;
                            } else {
                                $value = $option->title;
                            }
                        }
                        // TODO: possibly remove $value setting above
                        foreach ($values as &$selectionValueElement) {
                            if (isset($valuesIndex[$selectionValueElement->id])) {
                                $value = $selectionValueElement->title;
                            }
                        }
                    }
                    $this->parameterValuesIdIndex[$parameter->id] = $value;
                }
            }
        }
        return $this->parameterValuesIdIndex;
    }

    /**
     * @param $id
     * @return string
     */
    public function getParameterValueById($id)
    {
        $value = '';
        if ($info = $this->getParameterInfoById($id)) {
            if ($info['structureType'] == 'productSelection') {
                $options = [];
                foreach ($info['productOptions'] as $optionInfo) {
                    $options[] = $optionInfo['title'];
                };
                $value = implode(', ', $options);
            } elseif ($info['structureType'] == 'productParameter') {
                $value = $info['value'];
            }
        }
        return $value;
    }

    public function getParameterInfoById($id)
    {
        if ($primaryParametersInfo = $this->getPrimaryParametersInfo()) {
            foreach ($primaryParametersInfo as &$parameterInfo) {
                if ($parameterInfo['id'] == $id) {
                    return $parameterInfo;
                }
            }
        }
        if ($parametersInfoList = $this->getParametersInfoList()) {
            foreach ($parametersInfoList as &$parameterInfo) {
                if ($parameterInfo['id'] == $id) {
                    return $parameterInfo;
                }
            }
        }
        return false;
    }

    public function getProductConnectedCategories()
    {
        if (is_null($this->connectedProductCategories)) {
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            $this->connectedProductCategories = [];
            if ($connectedIdList = $linksManager->getConnectedIdList($this->id, 'connectedCategory', 'child')) {
                foreach ($connectedIdList as &$connectedId) {
                    if ($categoryElement = $structureManager->getElementById($connectedId)) {
                        $this->connectedProductCategories[] = $categoryElement->id;
                    }
                }
            }
        }
        return $this->connectedProductCategories;
    }

    public function getShuffledProductFromConnectedCategories()
    {
        $products = [];
        if ($categories = $this->getProductConnectedCategories()) {
            if ($quantity = $this->qtFromConnectedCategories) {

                $structureManager = $this->getService('structureManager');

                $db = $this->getService('db');
                $records = $db->table('structure_links')
                    ->select('childStructureId')
                    ->where('type', '=', 'catalogue')
                    ->whereIn('parentStructureId', $categories)
                    ->orderByRaw("RAND()")
                    ->take($quantity)
                    ->get();
                foreach ($records as $record) {
                    $products[] = $structureManager->getElementById($record['childStructureId']);
                }
            }
        }

        return $products;
    }

    public function getConnectedProducts()
    {
        if (is_null($this->connectedProducts)) {
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            $this->connectedProducts = [];
            if ($connectedIdList = $linksManager->getConnectedIdList($this->id, 'connected', 'child')) {
                foreach ($connectedIdList as &$connectedId) {
                    if ($productElement = $structureManager->getElementById($connectedId)) {
                        if ($productElement->inactive == '0' && ($productElement->isPurchasable() || $productElement->availability == "inquirable")) {
                            $this->connectedProducts[] = $productElement;
                        }
                    }
                }
            }
        }
        return $this->connectedProducts;
    }

    public function getConnectedProducts2()
    {
        if (is_null($this->connectedProducts2)) {
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            $this->connectedProducts2 = [];
            if ($connectedIdList = $linksManager->getConnectedIdList($this->id, 'connected2', 'child')) {
                foreach ($connectedIdList as &$connectedId) {
                    if ($productElement = $structureManager->getElementById($connectedId)) {
                        if ($productElement->inactive == '0' && ($productElement->isPurchasable() || $productElement->availability == "inquirable")) {
                            $this->connectedProducts2[] = $productElement;
                        }
                    }
                }
            }
        }
        return $this->connectedProducts2;
    }

    public function getConnectedProducts3()
    {
        if (is_null($this->connectedProducts3)) {
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            $this->connectedProducts3 = [];
            if ($connectedIdList = $linksManager->getConnectedIdList($this->id, 'connected3', 'child')) {
                foreach ($connectedIdList as &$connectedId) {
                    if ($productElement = $structureManager->getElementById($connectedId)) {
                        if ($productElement->inactive == '0' && ($productElement->isPurchasable() || $productElement->availability == "inquirable")) {
                            $this->connectedProducts3[] = $productElement;
                        }
                    }
                }
            }
        }
        return $this->connectedProducts3;
    }

    public function getDeliveryStatus()
    {
        if (!$this->deliveryStatus) {
            $this->deliveryStatus = $this->getRequestedParentCategory()->getInheritableProperty('deliveryStatus');

            //check all other possible categories if requested doesn't have one
            if (!$this->deliveryStatus) {
                foreach ($this->getConnectedCategories() as $category) {
                    if ($this->deliveryStatus = $category->getInheritableProperty('deliveryStatus')) {
                        break;
                    }
                }
            }
        }
        return $this->deliveryStatus;
    }

    public function copyExtraData($oldId)
    {
        parent::copyExtraData($oldId);
        $this->code = $this->code . "copy";
        // copy parameter values
        $collection = persistableCollection::getInstance('module_product_parameter_value');
        if ($valuesList = $collection->load(['productId' => $oldId])) {
            foreach ($valuesList as &$valueObject) {
                $newRecord = $collection->getEmptyObject();
                $newRecord->productId = $this->id;
                $newRecord->parameterId = $valueObject->parameterId;
                $newRecord->languageId = $valueObject->languageId;
                $newRecord->value = $valueObject->value;
                $newRecord->persist();
            }
        }
        // copy deliveries related
        $collection = persistableCollection::getInstance('delivery_type_inactive');
        if ($valuesList = $collection->load(["targetId" => $oldId])) {
            foreach ($valuesList as &$valueObject) {
                $newRecord = $collection->getEmptyObject();
                $newRecord->targetId = $this->id;
                $newRecord->deliveryTypeId = $valueObject->deliveryTypeId;
                $newRecord->persist();
            }
        }
        $collection = persistableCollection::getInstance('delivery_type_price');
        if ($valuesList = $collection->load(["targetId" => $oldId])) {
            foreach ($valuesList as &$valueObject) {
                $newRecord = $collection->getEmptyObject();
                $newRecord->targetId = $this->id;
                $newRecord->deliveryTypeId = $valueObject->deliveryTypeId;
                $newRecord->price = $valueObject->price;
                $newRecord->persist();
            }
        }
    }

    public function getParametersList()
    {
        $parametersList = [];
        if ($groups = $this->getParametersGroups()) {
            foreach ($groups as &$group) {
                if ($groupParameters = $group->getParametersList()) {
                    $parametersList = array_merge($parametersList, $groupParameters);
                }
            }
        }
        return $parametersList;
    }

    public function getPrimaryParametersInfo()
    {
        if ($this->primaryParametersInfo === null) {
            /**
             * @var ParametersManager $parametersManager
             */
            $parametersManager = $this->getService('ParametersManager');
            $this->primaryParametersInfo = $parametersManager->getProductPrimaryParametersInfo($this->id);
        }
        return $this->primaryParametersInfo;
    }

    public function getConnectedAdminCategories()
    {
        $categories = [];
        $linksManager = $this->getService('linksManager');
        $structureManager = $this->getService('structureManager');
        foreach ($linksManager->getConnectedIdList($this->id, 'catalogue', 'child') as $connectedId) {
            $element = $structureManager->getElementById($connectedId);
            if ($element && $element->structureType == 'category') {
                $categories[] = $element;
            }
        }
        return $categories;
    }

    public function getXmlSourcesCodeNames()
    {
        if ($this->xmlSourcesCodeNames === null) {
            $this->xmlSourcesCodeNames = [];
            $originsCollection = persistableCollection::getInstance('import_origin');
            $conditions = [
                [
                    'elementId',
                    '=',
                    $this->id,
                ],
            ];
            $records = $originsCollection->conditionalLoad(['importOrigin'], $conditions);
            if ($records) {
                foreach ($records as &$record) {
                    $this->xmlSourcesCodeNames[] = $record['importOrigin'];
                }
            }
        }
        return $this->xmlSourcesCodeNames;
    }

    public function setXmlSourcesCodeNames($xmlSourcesCodeNames)
    {
        $this->xmlSourcesCodeNames = $xmlSourcesCodeNames;
    }

    public function getImportPriceDifference()
    {
        $result = '+0%';
        if ($this->importPrice != '' && $this->importPrice != $this->price) {
            $difference = $this->price - $this->importPrice;
            $result = 100 / ($this->importPrice / $difference);
            $result = round($result, 2);
            if ($result >= 0) {
                $result = '+' . $result;
            }
            $result .= '%';
        }
        return $result;
    }

    public function getXmlCategoriesInfo()
    {
        $result = [];
        $collection = persistableCollection::getInstance('product_import_categories');
        $records = $collection->conditionalLoad(['productId', 'warehouseCode', 'warehouseCategoryTitle'],
            [
                [
                    'productId',
                    '=',
                    $this->id,
                ],
            ]);
        if ($records) {
            $result = $records;
        }
        return $result;
    }

    public function getCanonicalUrl()
    {
        if ($this->canonicalUrl) {
            return $this->canonicalUrl;
        }
        if ($categories = $this->getConnectedCategories()) {
            $category = reset($categories);
            return $category->URL . $this->structureName . '/';
        }
        return $this->URL;
    }

    public function getNextProduct()
    {
        if ($this->nextProduct === null) {
            $this->loadResidingProducts();
        }
        return $this->nextProduct;
    }

    public function getPreviousProduct()
    {
        if ($this->previousProduct === null) {
            $this->loadResidingProducts();
        }
        return $this->previousProduct;
    }

    protected function loadResidingProducts()
    {
        if ($category = $this->getRequestedParentCategory()) {
            if ($result = $category->getResidingProducts($this->id)) {
                if ($result['next']) {
                    $this->nextProduct = $result['next'];
                }
                if ($result['previous']) {
                    $this->previousProduct = $result['previous'];
                }
            }
        }
    }

    private function getFirstDataFromParents($parameterName)
    {
        $categories = [];

        foreach ($this->getConnectedCategories() as $category) {
            $element = $category;
            $level = 0;

            if ($category->{$parameterName}) {
                $parameter = $category->{$parameterName};
                $parameterLevel = 0;
            } else {
                $parameter = '';
                $parameterLevel = false;
            }

            while ($element) {
                $element = $element->getCurrentParentElement();
                if ($element && $element->structureType == 'category') {
                    $level++;

                    if (!$parameter && $element->{$parameterName}) {
                        $parameter = $element->{$parameterName};
                        $parameterLevel = $level;
                    }
                }
            }

            if ($parameter) {
                $categories[$level][$parameterLevel] = $parameter;
            }
        }

        krsort($categories);
        $parameters = array_shift($categories);
        if ($parameters) {
            ksort($parameters);
            $parameter = array_shift($parameters);
        } else {
            $parameter = '';
        }

        return $parameter;
    }

    public function getUnit()
    {
        if ($this->productUnit === null) {
            if ($this->unit) {
                $this->productUnit = $this->unit;
            } else {
                $this->productUnit = $this->getFirstDataFromParents('unit');
            }
        }

        return $this->productUnit;
    }

    private function getCategoryMetaDescriptionTemplate()
    {
        return $this->getFirstDataFromParents('metaDescriptionTemplate');
    }

    private function getCategoryMetaTitleTemplate()
    {
        return $this->getFirstDataFromParents('metaTitleTemplate');
    }

    private function getCategoryMetaH1Template()
    {
        return $this->getFirstDataFromParents('metaH1Template');
    }

    public function getTemplatedMetaTitle()
    {
        if ($categoryMetaTitleTemplate = $this->getCategoryMetaTitleTemplate()) {
            return $this->populateSeoTemplate($categoryMetaTitleTemplate);
        }
        return '';
    }

    public function getInfluentialSelections()
    {
        $result = &$this->influentialSelections;
        if ($result === null) {
            $result = $this->getBasketSelectionsInfo();
            // filter out non influential options, ensure certain order
            foreach ($result as $key => &$selection) {
                if (empty($selection['influential'])) {
                    unset($result[$key]);
                    continue;
                }
            }
            ksort($result);
            $result = array_values($result);
        }
        return $result;
    }

    public function getSelectionInfoForPricingForm($selectionIndex)
    {
        $result = [];
        $selections = $this->getInfluentialSelections();
        if ($selections && count($selections) >= $selectionIndex) {
            $selection = $selections[$selectionIndex];
            $result = $this->parseSelectionForPricingForm($selection);
        }
        return $result;
    }

    protected function parseSelectionForPricingForm($selection)
    {
        $result = [];
        foreach ($selection['productOptions'] as $option) {
            $title = $selection['title'] . ': ' . $option['title'];
            $result[] = [
                'code' => $option['id'] . ';',
                'title' => $title,
            ];
        }
        return $result;
    }

    public function getSelectionsPricingsMap()
    {
        $result = &$this->selectionsPricingMap;
        if ($result === null) {
            $result = $this->getService('ProductOptionsPricesManager')->getData($this->id);
        }
        return $result;
    }

    public function getSelectionPriceByUncertainCombo($comboCode)
    {
        $options = array_filter(explode(';', $comboCode));
        $comboCode = $this->generateOptionsComboCode($options);
        return $this->getSelectionPriceByCombo($comboCode);
    }

    public function getSelectionPriceByCombo($comboCode)
    {
        $map = $this->getSelectionsPricingsMap();
        return isset($map[$comboCode]) ? $map[$comboCode] : '';
    }

    public function generateOptionsComboCode($options)
    {
        sort($options);
        return implode(';', $options) . ';';
    }

    public function getOptionsComboGroupsForPricingForm()
    {
        $result = [];
        $selections = $this->getInfluentialSelections();
        if (count($selections) > 2) {
            array_shift($selections);
            array_shift($selections);
            $previousGroups = [];
            foreach ($selections as $selection) {
                $result = $optionsInfo = $this->parseSelectionForPricingForm($selection);
                if ($previousGroups) {
                    $result = [];
                    foreach ($previousGroups as $oldGroup) {
                        foreach ($optionsInfo as $selectionInfoItem) {
                            $result[] = [
                                'code' => $oldGroup['code'] . $selectionInfoItem['code'],
                                'title' => $oldGroup['title'] . ', ' . $selectionInfoItem['title'],
                            ];
                        }
                    }
                }
                $previousGroups = $result;
            }
        }
        return $result;
    }

    /**
     * @deprecated - use getElementData() instead
     */
    public function getProductDetailsJsData()
    {
        return $this->getElementData();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getElementData()
    {
        $selectionsPricings = $this->getSelectionsPricingsMap();
        $selectionsOldPricings = [];
        if ($selectionsPricings) {
            $discountsManager = $this->getService('shoppingBasketDiscounts');
            $mainConfig = $this->getService('ConfigManager')->getConfig('main');
            $vatRateSetting = $mainConfig->get('vatRate');
            $vatIncluded = $this->vatIncluded || $mainConfig->get('pricesContainVat') === true;
            $currencySelector = $this->getService('CurrencySelector');
            foreach ($selectionsPricings as $combo => &$price) {
                if (!$vatIncluded) {
                    $price *= $vatRateSetting;
                }

                $selectionsOldPricings[$combo] = sprintf('%01.2f', $price);

                $discountAmount = $discountsManager->getProductDiscount($this->id, $price);
                if ($discountAmount) {
                    $price -= $discountAmount;
                }
                $price = $currencySelector->convertPrice($price);
                $selectionsPricings[$combo] = sprintf('%01.2f', $price);
            }
        }
        $languageManager = $this->getService('languagesManager');
        $defaultLanguage = $languageManager->getDefaultLanguage('adminLanguages');
        $brandElement = $this->getBrandElement();
        $categoryElement = $this->getRequestedParentCategory();
        return [
            'id' => $this->id,
            'price' => $this->getPrice(),
            'name' => $this->getTitle(),
            'oldPrice' => $this->getOldPrice(),
            'category' => $categoryElement->getTitle(),
            'brand' => $brandElement ? $brandElement->getTitle() : $brandElement,
            'selectionsPricings' => $selectionsPricings ?: new stdClass(),
            'selectionsOldPricings' => $selectionsOldPricings ?: new stdClass(),
            'selectionsImages' => $this->getOptionsImagesInfo() ?: new stdClass(),
            'name_ga' => $this->getValue('title', $defaultLanguage->id),
            'category_ga' => $categoryElement->getValue('title', $defaultLanguage->id),
            'brand_ga' => $brandElement ? $brandElement->getValue('title', $defaultLanguage->id) : '',
        ];
    }

    public function getPriceBySelectedOptions($options)
    {
        $combo = $this->generateOptionsComboCode($options);
        return $this->getSelectionPriceByCombo($combo);
    }

    public function getTitle()
    {
        if (!empty($this->title)) {
            return $this->title;
        } else {
            return '';
        }
    }

    public function isOptionsPricingTabAvailable()
    {
        return count($this->getSelectionInfoForPricingForm(0)) > 0;
    }

    public function isOptionsImagesTabAvailable()
    {
        return !!$this->getBasketSelectionsInfo();
    }

    public function getOptionsImagesInfo()
    {
        return $this->getService('ProductOptionsImagesManager')->getData($this->id);
    }

    public function isEmptyPrice()
    {
        return ($this->price === '') ? true : false;
    }

    public function getLdJsonScriptHtml()
    {
        $tagText = '';
        if ($ldJsonData = $this->getLdJsonScriptData()) {
            $tagText = '<script type="application/ld+json">' . json_encode($ldJsonData) . '</script>';
        }
        return $tagText;
    }

    public function getLdJsonScriptData()
    {
        $data = [
            "@context" => "http://schema.org/",
            "@type" => "Product",
            "name" => $this->title,
            "sku" => $this->code,
            "url" => $this->URL,
        ];
        $data["description"] = $this->getTextContent();
        if ($brand = $this->getBrandElement()) {
            $data['brand'] = [
                '@type' => 'Thing',
                'name' => $brand->title,
            ];
        }
        if ($imageUrl = $this->getImageUrl()) {
            $data['image'] = $imageUrl;
        }

        $currencySelector = $this->getService('CurrencySelector');

        $offer = [
            '@type' => 'Offer',
            'priceCurrency' => strtoupper($currencySelector->getSelectedCurrencyCode()),
            'price' => $this->getPrice(),
        ];
        if ($this->availability == 'available' || $this->availability == 'available_inquirable' || ($this->availability == 'quantity_dependent' && $this->quantity > 0)) {
            $offer['availability'] = "http://schema.org/InStock";
        } elseif ($this->availability == 'inquirable') {
            $offer['availability'] = "http://schema.org/PreOrder";
        } else {
            $offer['availability'] = "http://schema.org/OutOfStock";
        }
        $data['offers'][] = $offer;
        return $data;
    }

    public function getImageUrl()
    {
        if ($image = $this->getFirstImageElement()) {
            $controller = controller::getInstance();
            return $controller->baseURL . 'image/type:galleryFullImage/id:' . $image->id . '/filename:' . $image->originalName;
        }
        return false;
    }

    public function showFeedbackForm()
    {
        $controller = controller::getInstance();
        $configManager = $controller->getConfigManager();
        return $configManager->get('product.showFeedbackForm');
    }

    public function getImagesLinkType()
    {
        //legacy-support, use trait's method instead
        return 'structure';
    }

    public function getDeliveryTypesInfo()
    {
        if ($this->deliveryTypesInfo === null) {
            $this->deliveryTypesInfo = [];
            $structureManager = $this->getService('structureManager');

            $deliveryTypesElementId = $structureManager->getElementIdByMarker('deliveryTypes');
            $deliveryTypeElementsIds = $this->getService('linksManager')
                ->getConnectedIdList($deliveryTypesElementId, 'structure', 'parent');

            if ($deliveryTypeElementsIds && $deliveryTypeElements = $structureManager->getElementsByIdList($deliveryTypeElementsIds, false, true)) {
                $inactiveDeliveriesRecords = $this->getDisabledDeliveryTypesRecords();
                $currencySelector = $this->getService('CurrencySelector');

                foreach ($deliveryTypeElements as &$deliveryTypeElement) {
                    if (!isset($inactiveDeliveriesRecords[$deliveryTypeElement->id])) {
                        $deliveryTypeInfo = [];
                        $deliveryTypeInfo["element"] = $deliveryTypeElement;
                        $priceExtra = $this->getDeliveryPriceExtra($deliveryTypeElement->id);
                        $deliveryTypeInfo["minPrice"] = $currencySelector->convertPrice($deliveryTypeElement->getMinPrice() + $priceExtra);
                        $deliveryTypeInfo["maxPrice"] = $currencySelector->convertPrice($deliveryTypeElement->getMaxPrice() + $priceExtra);
                        $this->deliveryTypesInfo[] = $deliveryTypeInfo;
                    }
                }
            }
        }
        return $this->deliveryTypesInfo;
    }

    public function getCountriesList()
    {
        $pricesIndex = $this->getPricesIndex();
        $structureManager = $this->getService('structureManager');
        $countriesList = [];
        if ($deliveryCountries = $structureManager->getElementByMarker('deliveryCountries')) {
            $countriesList = $structureManager->getElementsChildren($deliveryCountries->id);

            foreach ($countriesList as &$country) {
                if ($country->citiesList = $structureManager->getElementsChildren($country->id)) {
                    foreach ($country->citiesList as $city) {
                        if (isset($pricesIndex[$city->id])) {
                            $city->selected = true;
                            $city->deliveryPrice = $pricesIndex[$city->id]->price;
                        } else {
                            $city->selected = false;
                        }
                    }
                }

                if (isset($pricesIndex[$country->id])) {
                    $country->selected = true;
                    $country->deliveryPrice = $pricesIndex[$country->id]->price;
                } else {
                    $country->selected = false;
                }
            }
        }
        return $countriesList;
    }

    public function getOpenGraphData()
    {
        $data = [
            'title' => $this->title,
            'url' => $this->URL,
            'description' => $this->getMetaDescription(),
            'type' => 'product',
            'image' => '',
        ];
        if ($this->image) {
            $data['image'] = $this->getImageUrl();
        }
        return $data;
    }

    public function getTwitterData()
    {
        $data = [
            'card' => 'summary_large_image',
            'description' => $this->getMetaDescription(),
            'title' => $this->title,
            'url' => $this->URL,
            'image' => '',
        ];
        if ($this->image) {
            $data['image'] = $this->getImageUrl();
        }
        return $data;
    }


    public function getSearchTitle()
    {

        $title = $this->getTitle();

        if ($category = $this->getParentCategory()) {
            $title .= ' (' . $category->getTitle() . ')';
        }
        return $title;
    }

    /**
     * @return categoryElement|mixed|null
     */
    public function getParentCategory()
    {
        if ($this->parentCategory === null) {
            /**
             * @var structureManager $structureManager
             */
            $structureManager = $this->getService('structureManager');
            if ($parentsList = $structureManager->getElementsParents($this->id, false, 'catalogue')) {
                $this->parentCategory = reset($parentElement);
            }
        }
        return $this->parentCategory;
    }

    public function persistElementData()
    {
        if ($this->getService('ConfigManager')->get('product.useCodeForUrlName')) {
            $this->structureName = $this->code;
        }
        parent::persistElementData();
    }
}
