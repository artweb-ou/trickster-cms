<?php

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

/**
 * Class selectedProductsElement
 *
 * @property int $amountOnPageEnabled
 * @property int $selectionType
 * @property int $autoSelectionType
 * @property int $filterCategory
 * @property int $filterBrand
 * @property int $filterDiscount
 * @property int $filterPriceEnabled
 * @property int $availabilityFilterEnabled
 * @property int $amount
 */
class selectedProductsElement extends ProductsListElement implements ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    use ConnectedCategoriesProviderTrait;
    use ConnectedIconsProviderTrait;
    public $dataResourceName = 'module_selected_products';
    public $defaultActionName = 'show';
    public $role = 'content';
    
    protected $connectedBrands;
    protected $connectedBrandsIds;
    protected $connectedDiscounts;
    protected $connectedDiscountsIds;
    protected $connectedProductsIds;
    protected $connectedParameters;

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
        $moduleStructure['filterPriceEnabled'] = 'checkbox';
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
        $moduleStructure['buttonTitle'] = 'text';
        $moduleStructure['buttonUrl'] = 'url';
        $moduleStructure['buttonConnectedMenu'] = 'text';
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
        if ($this->productsListBaseQuery !== null) {
            return $this->productsListBaseQuery;
        }
        $productsListBaseQuery = $this->getProductsQuery();

        if ($connectedCategories = $this->getConnectedCategories()) {
            $subCategoriesIdIndex = [];
            foreach ($connectedCategories as $category) {
                $category->gatherSubCategoriesIdIndex($category->id, $subCategoriesIdIndex);
            }
            $deepCategoryIds = array_keys($subCategoriesIdIndex);
            $productsListBaseQuery->whereIn('module_product.id', function ($query) use ($deepCategoryIds) {
                /**
                 * @var Builder $query
                 */
                $query->from('structure_links')
                    ->select('childStructureId')
                    ->whereIn('parentStructureId', $deepCategoryIds)
                    ->where('type', '=', 'catalogue');
            });
        } else {
            //if no categories are selected then at least ensure that only products connected to any categories at all are used
            $productsListBaseQuery->whereIn('module_product.id', function ($query) {
                /**
                 * @var Builder $query
                 */
                $query->from('structure_links')
                    ->select('childStructureId')
                    ->where('type', '=', 'catalogue');
            });
        }
        if ($brandIds = $this->getConnectedBrandsIds()) {
            $productsListBaseQuery->whereIn('module_product.brandId', $brandIds);
        }

        if ($discountIds = $this->getConnectedDiscountsIds()) {
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
            $productsListBaseQuery->whereIn('module_product.id', $discountedProductIds);
        }
        if ($limitingIconIds = $this->getConnectedIconsIds()) {
            /**
             * @var ProductIconsManager $productIconsManager
             */
            $productIconsManager = $this->getService('ProductIconsManager');
            $connectedIconsProducts = [];
            foreach ($limitingIconIds as $iconId) {
                if ($iconProducts = $productIconsManager->getIconProductIds($iconId)) {
                    $connectedIconsProducts = array_merge($iconProducts, $connectedIconsProducts);
                }
            }
            $productsListBaseQuery->whereIn('module_product.id', $connectedIconsProducts);
        }


        if ($this->selectionType) { // manual selection - prepare the connected products
            /**
             * @var linksManager $linksManager
             */
            $linksManager = $this->getService('linksManager');
            $connectedProductIds = $linksManager->getConnectedIdList($this->id, 'selectedProducts', 'parent');
            $productsListBaseQuery->whereIn('module_product.id', $connectedProductIds);
        } else {
            /**
             * @var Connection $db
             */
            $db = $this->getService('db');
            switch ($this->autoSelectionType) {
                // date
                case 0:
                    if ($this->amount) {
                        $idList = [];
                        if ($records = $db->table('structure_elements')
                            ->where('structureType', '=', 'product')
                            ->orderBy('dateCreated', 'desc')
                            ->limit($this->amount)
                            ->select('id')
                            ->get()) {
                            $idList = array_column($records, 'id');
                        }

                        $productsListBaseQuery->whereIn('module_product.id', $idList);
                    }
                    break;

                // popularity (purchases)
                case 1:
                    $query = $this->getProductsQuery();
                    if ($this->amount) {
                        $query->limit($this->amount);
                    }
                    $idList = [];
                    if ($records = $query
                        ->orderBy('purchaseCount', 'desc')
                        ->get()) {
                        $idList = array_column($records, 'id');
                    }

                    $productsListBaseQuery->whereIn('module_product.id', $idList);
                    break;

                // purchased latest
                case 2:
                    $query = $this->getProductsQuery();
                    if ($this->amount) {
                        $query->limit($this->amount);
                    }
                    $idList = [];
                    if ($records = $query
                        ->orderBy('lastPurchaseDate', 'desc')
                        ->get()) {
                        $idList = array_column($records, 'id');
                    }

                    $productsListBaseQuery->whereIn('module_product.id', $idList);
                    break;
                // random discounted products
                case 3:

                    $query = $this->getProductsQuery();
                    if ($this->amount) {
                        $query->limit($this->amount);
                    }
                    $idList = [];
                    if ($records = $query
                        ->where('oldPrice', '<>', 0)
                        ->inRandomOrder()
                        ->get()) {
                        $idList = array_column($records, 'id');
                    }

                    $productsListBaseQuery->whereIn('module_product.id', $idList);
                    break;
                // all available products
                case 4:
                    break;
                default:
                    break;
            }

        }
        $this->productsListBaseQuery = $productsListBaseQuery;
        return $this->productsListBaseQuery;

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

    public function getConnectedBrands()
    {
        if ($this->connectedBrands === null) {
            $this->connectedBrands = [];
            if ($brandIds = $this->getConnectedBrandsIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($brandIds as &$brandId) {
                    /**
                     * @var brandElement $brandElement
                     */
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

    public function getConnectedBrandsIds()
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
                    /**
                     * @var discountElement $discountElement
                     */
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

    public function getDiscounts()
    {
        return $this->getService('structureManager')
            ->getElementsByType('discount', $this->getService('LanguagesManager')
                ->getCurrentLanguageId());
    }

    public function getBrands()
    {
        return $this->getService('structureManager')->getElementsByType('brand', $this->getService('LanguagesManager')
            ->getCurrentLanguageId());
    }

    public function isFilterableByType($filterType)
    {
        switch ($filterType) {
            case 'category':
                $result = ($this->filterCategory);
                break;
            case 'brand':
                $result = ($this->filterBrand);
                break;
            case 'discount':
                $result = ($this->filterDiscount);
                break;
            case 'parameter':
                $result = (bool)$this->getParameterSelectionsForFiltering();
                break;
            case 'price':
                $result = $this->filterPriceEnabled;
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

    protected function getProductsListFixedLimit()
    {
        return $this->amount;
    }

    public function getConnectedButtonMenu() {
        $linksManager = $this->getService('linksManager');
        $buttonConnectedMenuId = $linksManager->getConnectedIdList($this->id, "buttonConnectedMenu", "parent");
        $menus = $this->getDisplayMenusInfo();
        foreach ($menus as &$menu) {
            if($buttonConnectedMenuId[0] === $menu['id']) {
                $menu['select'] = true;
            }
        }
        return $menus;
    }

    public function getButtonConnectedMenuUrl() {
        $linksManager = $this->getService('linksManager');
        $connectedProductsIds = $linksManager->getConnectedIdList($this->id, "buttonConnectedMenu", "parent");
        if(!empty($connectedProductsIds)) {
            $structureManager = $this->getService('structureManager');
            $element = $structureManager->getElementById($connectedProductsIds[0]);
            if($element) {
                return $element->URL;
            }
        }
    }
}


