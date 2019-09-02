<?php

/**
 * Class brandElement
 *
 * @property int $amountOnPageEnabled
 * @property int $brandFilterEnabled
 * @property int $categoryFilterEnabled
 * @property int $discountFilterEnabled
 * @property int $parameterFilterEnabled
 * @property int $availabilityFilterEnabled
 */

class collectionElement extends ProductsListElement implements ImageUrlProviderInterface
{
    use ImageUrlProviderTrait;
    use ConfigurableLayoutsProviderTrait;

    public $dataResourceName = 'module_collection';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $defaultSortParameter = "price";
    protected $productsList;
    protected $brandsList;
    protected $categoriesList;
    public $feedbackFormsList = [];
    protected $productsPager;
    protected $productSelectionFilters;
    protected $parametersGroups;
    protected $selectedFilterValues;
    protected $selectedBrandsIdList;
    protected $selectedSortParameter;
    protected $discountsList;
    protected $parentCategory;
    protected $topProductsList;
    protected $sortParameters;
    protected $connectedProductsIds;
    protected $brandFiltered = false;
    public $selectedFilter;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'text';
        $moduleStructure['link'] = 'url';
        $moduleStructure['priceSortingEnabled'] = 'text';
        $moduleStructure['nameSortingEnabled'] = 'text';
        $moduleStructure['dateSortingEnabled'] = 'text';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['collectionsListIds'] = 'numbersArray';
        $moduleStructure['importInfo'] = 'numbersArray';
        $moduleStructure['availabilityFilterEnabled'] = 'checkbox';
        $moduleStructure['parameterFilterEnabled'] = 'checkbox';
        $moduleStructure['discountFilterEnabled'] = 'checkbox';
        $moduleStructure['amountOnPageEnabled'] = 'checkbox';
        $moduleStructure['categoryFilterEnable'] = 'checkbox';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'content';
        $multiLanguageFields[] = 'introduction';
        $multiLanguageFields[] = 'metaTitle';
        $multiLanguageFields[] = 'metaDescription';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSeoForm',
            'showPrivileges',
        ];
    }


    protected function getConnectedProductsIds()
    {
        /**
         * @var $linksManager linksManager
         */
        $linksManager = $this->getService('linksManager');
        $connectedProductsIds = $linksManager->getConnectedIdList($this->id, 'collectionProduct');
        return $connectedProductsIds;
    }

    /**
     * @return productElement[]
     */
    public function getConnectedProducts()
    {
        /**
         * @var $structureManager structureManager
         */
        $connectedProducts = [];
        $connectedProductIds = $this->getConnectedProductsIds();
        if (!empty($connectedProductIds)) {
            $structureManager = $this->getService('structureManager');
            foreach ($connectedProductIds as $productId) {
                $productElement = $structureManager->getElementById($productId);
                if (!empty($productElement)) {
                    $connectedProducts[] = $productElement;
                }
            }
        }
        return $connectedProducts;
    }

    public function getConnectedCollectionsListsIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'collections', 'child');
    }

    public function isFilterableByType($filterType)
    {
        switch ($filterType) {
            case 'category':
                $result = $this->categoryFilterEnable;
                break;
            case 'brand':
                $result = $this->brandFilterEnabled;
                break;
            case 'discount':
                $result = $this->discountFilterEnabled;
                break;
            case 'parameter':
                $result = $this->parameterFilterEnabled;
                break;
            case 'price':
                //todo: implement checkbox in admin form
                $result = false;
                break;
            case 'availability':
                $result = $this->availabilityFilterEnabled;
                break;
            default:
                $result = true;
        }

        return $result;
    }


    public function getProductsLayout()
    {
        $structureManager = $this->getService('structureManager');
        if ($collectionsList = $structureManager->getElementsFirstParent($this->id)) {
            return $collectionsList->getCurrentLayout('productsLayout');
        };
        return false;
    }

    public function getCollectionsLayout()
    {
        $structureManager = $this->getService('structureManager');
        if ($collectionsList = $structureManager->getElementsFirstParent($this->id)) {
            return $collectionsList->getCurrentLayout('collection');
        };
        return false;
    }

    public function getElementData()
    {
        $brandInfo = [];
        $brandInfo["id"] = $this->id;
        $brandInfo["title"] = $this->title;
        $brandInfo["URL"] = $this->URL;
        $brandInfo["image"] = controller::getInstance()->baseURL . "image/type:brandWidgetItem/id:" . $this->image . "/filename:" . $this->originalName;
        return $brandInfo;
    }

    public function isAmountSelectionEnabled()
    {
        return $this->amountOnPageEnabled;
    }

    protected function getProductsListBaseQuery()
    {
        /**
         * @var $linksManager linksManager
         */
        $linksManager = $this->getService('linksManager');
        if ($this->productsListBaseQuery !== null) {
            return $this->productsListBaseQuery;
        }
        $this->productsListBaseQuery = false;

        $connectedProductIds = $linksManager->getConnectedIdList($this->id, 'collectionProduct');
        $query = $this->getProductsQuery();
        $query->whereIn('id', $connectedProductIds);

        $this->productsListBaseQuery = $query;
        return $this->productsListBaseQuery;
    }

    public function getCategoryList()
    {
        /**
         * @var $structureManager structureManager
         */
        $structureManager = $this->getService('structureManager');
        $categories = $structureManager->getElementsByType('category');
        return $categories;
    }

    public function getProductsListCategories()
    {
        /**
         * @var $structureManager structureManager
         * @var $productCatalogue productCatalogueElement
         * @var $topCategoryElements categoryElement[]
         */
        $structureManager = $this->getService('structureManager');
        $topCategories = [];
        $allCategories = [];
        $connectedProducts = $this->getConnectedProducts();
        $currentLanguage = $connectedProducts[0]->getCurrentLanguage();
        $productCatalogue = $structureManager->getElementsByType('productCatalogue', $currentLanguage)[0];
        $topCategoryElements = $productCatalogue->getCategoriesList();
        foreach ($connectedProducts as $product) {
            foreach ($product->getConnectedCategories() as $category) {
                $this->processCategory($category, $allCategories, $topCategories);
            }
        }
        $categoryIds = array_keys($allCategories);
        $categories = [];
        foreach ($topCategoryElements as &$topCategory) {
            if(isset($topCategories[$topCategory->id])) {
                $level = 0;
                $topCategory->setLevel($level);
                $categories[] = $topCategory;
                $this->categoryFilter($topCategory, $categoryIds, $categories, $level);
            } else {
                unset($topCategory);
            }
        }
        return $categories;
    }

    /**
     * @param $category categoryElement
     * @param $allCategories categoryElement[]
     * @param $categories array
     * @param $level int
     */
    protected function categoryFilter($category, $allCategories, &$categories, $level) {
        if(in_array($category->id, $allCategories)) {
            if ($childCategories = $category->getChildCategories()) {
                $level ++;
                foreach ($childCategories as $childCategory) {
                    if (in_array($childCategory->id, $allCategories)) {
                        $childCategory->setLevel($level);
                        $categories[] = $childCategory;
                        $this->categoryFilter($childCategory, $allCategories, $categories, $level);
                    }
                }
            }
        }
    }

    /**
     * @param categoryElement $category
     * @param $allCategories
     * @param $topCategories
     */
    protected function processCategory($category, &$allCategories, &$topCategories)
    {
        if (!isset($allCategories[$category->id])) {
            $allCategories[$category->id] = $category;
            if ($parentCategories = $category->getParentCategories()) {
                foreach ($parentCategories as $parentCategory) {
                    $this->processCategory($parentCategory, $allCategories, $topCategories);
                }
            } else {
                $topCategories[$category->id] = $category;
            }
        }
    }
}