<?php

/**
 * Class productCatalogueElement
 *
 * @property string $columns
 */
class productCatalogueElement extends categoryStructureElement implements ConfigurableLayoutsProviderInterface, ColumnsTypeProvider
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_catalogue_filter';
    public $defaultActionName = 'show';
    public $role = 'hybrid';
    protected $replacementElements = [];
    protected $categoriesList;
    public $adminCategoriesList;
    protected $productsListParentElementsIds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['categories'] = 'numbersArray';
        $moduleStructure['columns'] = 'text';
        $moduleStructure['categoryLayout'] = 'text';
        $moduleStructure['productsLayout'] = 'text';
        $moduleStructure['categorized'] = 'checkbox';
        $moduleStructure['connectAllCategories'] = 'text';

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
        // tmp
        $moduleStructure['parameters'] = 'array';
        $moduleStructure['formRelativesInput'] = 'array';
    }

    protected function getTabsList()
    {
        return [
            'showFullList',
            'showForm',
            'showLayoutForm',
            'showSettingsForm',
            'showPositions',
            'showPrivileges',
            'showLanguageForm',
        ];
    }

    protected function getProductsListParentRestrictionId()
    {
        if (!$this->categorized) {
            return $this->id;
        } elseif ($containerElement = $this->getContainerElement()) {
            return $containerElement->id;
        }
        return 0;
    }

    public function setReplacementElements($list)
    {
        $this->replacementElements = $list;
    }

    public function getReplacementElements($roles)
    {
        $replacementElements = [];
        $contentRole = in_array('content', (array)$roles);
        if (!$contentRole || $this->categoryLayout !== 'hide') {
            $replacementElements = $this->replacementElements;
        }
        if ($contentRole) {
            if ($this->productsLayout !== 'hide') {
                $replacementElements[] = $this;
            }
        }
        return $replacementElements;
    }

    protected function getProductsListBaseQuery()
    {
        if ($this->productsListBaseQuery !== null) {
            return $this->productsListBaseQuery;
        }
        $this->productsListBaseQuery = [];

        $query = $this->getProductsQuery();

        $query->leftJoin('structure_links', 'module_product.id', '=', 'childStructureId');

        //include only the products connected to this category or include all subcategories as well
        if ($this->categorized) {
            if ($container = $this->getContainerElement()) {
                $categoriesIds = $this->getService('linksManager')->getConnectedIdList($container->id, 'catalogue', 'parent');
                $structureManager = $this->getService('structureManager');
                $categoriesIdIndex = [];

                foreach ($categoriesIds as &$categoryId) {
                    /**
                     * @var categoryElement $category
                     */
                    if ($category = $structureManager->getElementById($categoryId)) {
                        $category->gatherSubCategoriesIdIndex($this->id, $categoriesIdIndex);
                    }
                }
                $query->whereIn('parentStructureId', array_keys($categoriesIdIndex));
            }
        } else {
            $query->where('parentStructureId', '=', $this->id);
        }
        $query->where('type', '=', 'catalogue');
        $this->productsListBaseQuery = $query;
        return $this->productsListBaseQuery;
    }

    public function getConnectedProductsIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'productCatalogueProduct', 'parent');
    }

    protected function generatePagerUrl()
    {
        $url = parent::generatePagerUrl();
        if (!$this->final && $parentElement = $this->getService('structureManager')
                ->getElementsFirstParent($this->id)
        ) {
            $url = str_replace($this->URL, $parentElement->URL, $url);
        }
        return $url;
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
                $enabled = false;
                break;
            case 1:
                $enabled = true;
                break;
            case 2:
                $enabled = false;
        }
        return $enabled;
    }

    public function getContainerElement()
    {
        $containerElement = false;
        $structureManager = $this->getService('structureManager');
        if ($parentElements = $structureManager->getElementsParents($this->id)) {
            foreach ($parentElements as &$parentElement) {
                if ($parentElement->structureType == 'language' || $parentElement->structureType == 'folder') {
                    $containerElement = $parentElement;
                    break;
                }
            }
        }
        return $containerElement;
    }

    public function getInheritableProperty($propertyName)
    {
        return $this->$propertyName;
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

    public function getColumnsType()
    {
        return $this->columns;
    }

    public function getCategoriesList()
    {
        if ($this->categoriesList === null) {
            $this->categoriesList = [];
            $structureManager = $this->getService('structureManager');
            if ($firstParent = $structureManager->getElementsFirstParent($this->id)) {
                $this->categoriesList = $structureManager->getElementsChildren($firstParent->id, 'container', 'catalogue');
            }
        }
        return $this->categoriesList;
    }

    public function getTitle()
    {
        $title = '';

        $structureManager = $this->getService('structureManager');
        if ($firstParent = $structureManager->getElementsFirstParent($this->id)) {
            $title .= $firstParent->getTitle() . ' / ';
        }
        $title .= parent::getTitle();
        return $title;
    }
}