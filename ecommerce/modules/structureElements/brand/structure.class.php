<?php

class brandElement extends productsListStructureElement implements ImageUrlProviderInterface
{
    use ImageUrlProviderTrait;
    use ConfigurableLayoutsProviderTrait;

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

    public function getConnectedProductsIds()
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
                    $languagesManager = $this->getService('languagesManager');
                    $languageId = $languagesManager->getCurrentLanguageId();
                    foreach ($records as &$record) {
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

    public function getDefaultOrder()
    {
        return 'manual';
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
        foreach ($records as &$record) {
            $record->delete();
        }
        parent::deleteElementData();
    }

    public function isFilterableByAvailability()
    {
        return $this->availabilityFilterEnabled;
    }

    public function isFilterableByParameter()
    {
        return $this->parameterFilterEnabled;
    }

    public function isFilterableByDiscount()
    {
        return $this->discountFilterEnabled;
    }

    public function getProductsLayout()
    {
        $structureManager = $this->getService('structureManager');
        if ($brandsList = $structureManager->getElementsFirstParent($this->id)) {
            return $brandsList->getCurrentLayout('productsLayout');
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
}