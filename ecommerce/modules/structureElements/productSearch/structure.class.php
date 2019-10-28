<?php

/**
 * Class productSearchElement
 *
 * @property int $filterParameters
 * @property int $filterCategory
 * @property int $filterBrand
 * @property int $filterPrice
 * @property int $filterDiscount
 * @property int $availabilityFilterEnabled
 * @property int $pageDependent
 * @property int $sortingEnabled
 */
class productSearchElement extends menuDependantStructureElement
{
    use ProductFilterFactoryTrait;
    use JsonDataProviderElement;

    public $dataResourceName = 'module_productsearch';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'content';
    protected $productCatalogue;
    /**
     * @var ProductsListElement
     */
    protected $productsListElement;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['filterParameters'] = 'checkbox';
        $moduleStructure['filterCategory'] = 'checkbox';
        $moduleStructure['filterBrand'] = 'checkbox';
        $moduleStructure['filterPrice'] = 'checkbox';
        $moduleStructure['filterDiscount'] = 'checkbox';
        $moduleStructure['availabilityFilterEnabled'] = 'checkbox';
        $moduleStructure['sortingEnabled'] = 'checkbox';
        $moduleStructure['priceInterval'] = 'naturalNumber';

        $moduleStructure['pageDependent'] = 'checkbox';
        $moduleStructure['checkboxesForParameters'] = 'checkbox';
        $moduleStructure['pricePresets'] = 'checkbox';
        // temporary
        $moduleStructure['parametersIds'] = 'numbersArray';
        $moduleStructure['categoryId'] = 'text';
        $moduleStructure['brandId'] = 'text';
        $moduleStructure['selectionValues'] = 'array';
        $moduleStructure['parameterValues'] = 'array';
        $moduleStructure['catalogueFilterId'] = 'text';
    }

    public function isFilterableByType($filterType)
    {
        switch ($filterType) {
            case 'category':
                $result = $this->filterCategory;
                break;
            case 'brand':
                $result = $this->filterBrand;
                break;
            case 'discount':
                $result = $this->filterDiscount;
                break;
            case 'parameter':
                $result = false;
                if ($this->filterParameters) {
                    if (!$this->pageDependent) {
                        $result = $this->getConnectedParametersIds();
                    } elseif ($productsListElement = $this->getProductsListElement()) {
                        $result = $productsListElement->getParameterSelectionsForFiltering();
                    }
                }
                break;
            case 'price':
                $result = $this->filterPrice;
                break;
            case 'availability':
                $result = $this->availabilityFilterEnabled;
                break;
            default:
                $result = false;
        }

        return $result;
    }

    public function isFilterable()
    {
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->isFilterable();
        }
        return false;
    }

    public function isFieldSortable($field)
    {
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->isFieldSortable($field);
        }
        return false;
    }

    public function getFilterSort()
    {
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->getFilterSort();
        }
        return false;
    }

    public function getFilterOrder()
    {
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->getFilterOrder();
        }
        return false;
    }

    public function getFilteredUrl()
    {
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->getFilteredUrl();
        }
        return false;
    }

    public function getParameterSelectionsForFiltering()
    {
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->getParameterSelectionsForFiltering();
        }
        return false;
    }

    public function getCurrentElement()
    {
        return $this->getService('structureManager')->getCurrentElement(controller::getInstance()->requestedPath);
    }

    public function setProductsListElement(ProductsListElement $productsListElement)
    {
        $this->productsListElement = $productsListElement;
    }

    public function getProductsListElement()
    {
        if ($this->productsListElement === null) {
            $this->productsListElement = false;
            if ($this->pageDependent) {
                if ($currentElement = $this->getCurrentElement()) {
                    if ($currentElement instanceof productElement) {
                        $this->productsListElement = $this->getLastVisitedCategory();
                    } elseif ($currentElement instanceof productsListElement && $currentElement->structureType != 'productCatalogue'
                    ) {
                        $this->productsListElement = $currentElement;
                    }
                }
            }
            if (!$this->productsListElement) {
                $this->productsListElement = $this->getProductCatalogue();
            }
        }
        return $this->productsListElement;
    }

    public function canActLikeFilter()
    {
        $currentElement = $this->getCurrentElement();
        return ($this->pageDependent && (($currentElement && $currentElement instanceof productsListElement && $currentElement->structureType != 'productCatalogue') || ($currentElement instanceof productElement && $this->getLastVisitedCategory())));
    }

    public function getLastVisitedCategory()
    {
        $result = null;
        $categoryId = $this->getService('user')->getStorageAttribute('lastCategoryId');
        if ($categoryId) {
            $result = $this->getService('structureManager')->getElementById($categoryId);
        }
        return $result;
    }

    public function getProductCatalogue()
    {
        if ($this->productCatalogue === null) {
            $this->productCatalogue = false;
            if ($connectedCataloguesIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, 'productSearchCatalogue', 'parent')
            ) {
                $this->productCatalogue = $this->getService('structureManager')->getElementById($connectedCataloguesIds[0]);
            }
        }
        return $this->productCatalogue;
    }

    public function getConnectedParametersIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, "productSearchParameter", 'parent');
    }

    public function canBeDisplayed()
    {
        if (!$this->getProductsListElement()) {
            return false;
        }
        if (controller::getInstance()->getParameter('productSearch')) {
            return true;
        }
        if (($this->canActLikeFilter() || !$this->pageDependent) && $this->sortingEnabled) {
            return true;
        }
        $filterTypes = [
            'category',
            'parameter',
            'discount',
            'brand',
        ];
        if ($this->canActLikeFilter() || !$this->pageDependent) {
            $filterTypes[] = 'price';
        }
        foreach ($filterTypes as $type) {
            foreach ($this->getFiltersByType($type) as $filter) {
                return true;
            }
        }
        return false;
    }
}


