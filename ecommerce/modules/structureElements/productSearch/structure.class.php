<?php

/**
 * Class productSearchElement
 *
 * @property int $pageDependent
 * @property int $sortingEnabled
 */
class productSearchElement extends menuDependantStructureElement
{
    use ProductFilterFactoryTrait;
    public $dataResourceName = 'module_productsearch';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'content';
    protected $productCatalogue;
    protected $productsListElement;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
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
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->isFilterableByType($filterType);
        }
        return false;
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


//
//    public function isApplied()
//    {
//        $filterParameters = [
//            'productsearch',
//            'category',
//            'brand',
//            'discount',
//            'parameter',
//            'price',
//        ];
//        $controller = controller::getInstance();
//        foreach ($filterParameters as &$filterParameter) {
//            if ($controller->getParameter($filterParameter)) {
//                return true;
//            }
//        }
//        return false;
//    }
//
//    public function addFilter(productFilter $filter)
//    {
//        if ($this->baseFilter !== null) {
//            $this->baseFilter->addFilter($filter);
//        } else {
//            $this->baseFilter = $filter;
//        }
//        $this->registerFilter($filter);
//    }
//
//    protected function registerFilter(productFilter $filter)
//    {
//        $type = $filter->getType();
//        if (!isset($this->filtersIndex[$type])) {
//            $this->filtersIndex[$type] = [];
//        }
//        $this->filtersIndex[$type][] = $filter;
//    }
//
    public function getCurrentElement()
    {
        return $this->getService('structureManager')->getCurrentElement(controller::getInstance()->requestedPath);
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
                if ($filter->isRelevant()) {
                    return true;
                }
            }
        }
        return false;
    }
}


