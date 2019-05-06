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
    protected $categories;
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
        ];
    }

    protected function getParentRestrictionId()
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

    public function getProductsListParentElementsIds()
    {
        if ($this->productsListParentElementsIds === null) {
            $this->productsListParentElementsIds = [];
            if (!$this->categorized) {
                $this->productsListParentElementsIds = (array)$this->id;
            } elseif ($container = $this->getContainerElement()) {
                $categoriesIds = $this->getService('linksManager')
                    ->getConnectedIdList($container->id, 'catalogue', 'parent');
                $structureManager = $this->getService('structureManager');
                foreach ($categoriesIds as &$categoryId) {
                    $category = $structureManager->getElementById($categoryId);
                    if ($categoryProductsParentsIds = $category->getProductsListParentElementsIds()) {
                        $this->productsListParentElementsIds = array_merge($this->productsListParentElementsIds, $categoryProductsParentsIds);
                    }
                }
                $this->productsListParentElementsIds = array_unique($this->productsListParentElementsIds);
            }
        }
        return $this->productsListParentElementsIds;
    }

    public function getConnectedProductsIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'productCatalogueProduct', 'parent');
    }

    protected function generatePagerUrl(array $searchArguments)
    {
        $url = parent::generatePagerUrl($searchArguments);
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
}