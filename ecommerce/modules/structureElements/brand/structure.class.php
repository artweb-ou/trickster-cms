<?php

/**
 * Class brandElement
 *
 * @property string originalName
 * @property int $amountOnPageEnabled
 * @property int $brandFilterEnabled
 * @property int $discountFilterEnabled
 * @property int $parameterFilterEnabled
 * @property int $availabilityFilterEnabled
 */

class brandElement extends ProductsListElement implements ImageUrlProviderInterfacem, JsonDataProvider
{
    use ImageUrlProviderTrait;
    use ConfigurableLayoutsProviderTrait;
    use JsonDataProviderElement;

    public $dataResourceName = 'module_brand';
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
        $moduleStructure['brandsListsIds'] = 'numbersArray';
        $moduleStructure['importInfo'] = 'numbersArray';
        $moduleStructure['availabilityFilterEnabled'] = 'checkbox';
        $moduleStructure['parameterFilterEnabled'] = 'checkbox';
        $moduleStructure['discountFilterEnabled'] = 'checkbox';
        $moduleStructure['amountOnPageEnabled'] = 'checkbox';
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
        if (is_null($this->connectedProductsIds)) {
            $this->connectedProductsIds = [];

            $linksManager = $this->getService('linksManager');
            if ($connectedProductIds = $linksManager->getConnectedIdList($this->id, 'productbrand', 'parent')) {
                // check if all products are in a category
                $relevantProductIds = [];
                $collection = persistableCollection::getInstance('structure_links');
                $conditions = [
                    [
                        'childStructureId',
                        'in',
                        $connectedProductIds,
                    ],
                    [
                        'type',
                        'IN',
                        ['catalogue', 'productCatalogueProduct'],
                    ],
                ];

                if ($records = $collection->conditionalLoad([
                    'childStructureId',
                    'parentStructureId',
                ], $conditions)
                ) {
                    $structureManager = $this->getService('structureManager');
                    $languagesManager = $this->getService('LanguagesManager');
                    $languageId = $languagesManager->getCurrentLanguageId();
                    foreach ($records as $record) {
                        // check if the category/catalogue is available in current language
                        if ($structureManager->checkElementInParent($record['parentStructureId'], $languageId)) {
                            $relevantProductIds[] = $record['childStructureId'];
                        }
                    }
                    $this->connectedProductsIds = $relevantProductIds;
                }
            }
        }
        return $this->connectedProductsIds;
    }

    public function getConnectedBrandsListsIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'brands', 'child');
    }

    public function connectWithAutomaticBrandsLists()
    {
        $structureManager = $this->getService('structureManager');
        if ($brandsLists = $structureManager->getElementsByType('brandsList')) {
            $linksManager = $this->getService('linksManager');
            foreach ($brandsLists as &$brandsList) {
                if ($brandsList->connectAll) {
                    $linksManager->linkElements($brandsList->id, $this->id, 'brands');
                }
            }
        }
    }

    public function deleteElementData()
    {
        $collection = persistableCollection::getInstance('import_origin');
        $searchFields = ['elementId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as $record) {
            $record->delete();
        }
        parent::deleteElementData();
    }

    public function isFilterableByType($filterType)
    {
        switch ($filterType) {
            case 'category':
                //todo: implement checkbox in admin form
                $result = true;
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
        if ($brandsList = $structureManager->getElementsFirstParent($this->id)) {
            return $brandsList->getCurrentLayout('productsLayout');
        };
        return false;
    }

    public function isAmountSelectionEnabled()
    {
        return $this->amountOnPageEnabled;
    }

    protected function getProductsListBaseQuery()
    {
        if ($this->productsListBaseQuery !== null) {
            return $this->productsListBaseQuery;
        }
        $this->productsListBaseQuery = false;

        $query = $this->getProductsQuery();
        $query->where('brandId', '=', $this->id);

        $this->productsListBaseQuery = $query;
        return $this->productsListBaseQuery;
    }
}