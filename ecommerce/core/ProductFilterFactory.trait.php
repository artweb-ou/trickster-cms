<?php

trait ProductFilterFactoryTrait
{
    /**
     * @var ProductFilter[]
     */
    protected $filters;
    /**
     * @var ProductFilter[][]
     */
    protected $filtersIndex = [];
    /**
     * @var ProductFilter[]
     */
    protected $filtersIdIndex = [];

    public function createProductFilter($type, $initialOptions = null)
    {
        $className = ucfirst($type) . 'ProductFilter';
        /**
         * @var ProductFilter $filter
         */
        $filter = new $className($this->getProductsListElement(), $initialOptions);
        if ($filter instanceof DependencyInjectionContextInterface) {
            $this->instantiateContext($filter);
        }
        return $filter;
    }

    /**
     * @return ProductFilter[]
     */
    public function getFilters()
    {
        if ($this->filters === null) {
            $this->filters = [];
            if ($this->isFilterable()) {
                $filterTypes = ['category', 'brand', 'discount', 'availability'];
                foreach ($filterTypes as $filterType) {
                    if ($this->isFilterableByType($filterType)) {
                        $this->addFilter($this->createProductFilter($filterType));
                    }
                }
                if ($this->isFilterableByType('parameter')) {
                    $parameters = $this->getParameterSelectionsForFiltering();
                    foreach ($parameters as $parameter) {
                        $this->addFilter($this->createProductFilter('parameter', ['selectionElement' => $parameter]));
                    }
                }
                if ($this->isFilterableByType('price')) {
                    $this->addFilter($filter = $this->createProductFilter('price', ['usePresets' => $this->pricePresets]));
                }
                /**
                 * @var Cache $cache
                 */
                $cache = $this->getService('Cache');
                $key = $this->getProductsListElement()->getCacheKey();
                if (($optionsInfoIndex = $cache->get($this->id . ':options:' . $key)) !== false) {
                    foreach ($optionsInfoIndex as $id => $optionsInfo) {
                        if (isset($this->filtersIdIndex[$id])) {
                            $this->filtersIdIndex[$id]->setOptionsInfo($optionsInfo);
                        }
                    }
                } else {
                    $optionsInfoList = [];
                    foreach ($this->filters as $filter) {
                        $optionsInfoList[$filter->getId()] = $filter->getOptionsInfo();
                    }
                    $cache->set($this->id . ':options:' . $key, $optionsInfoList);
                }

            }
        }
        return $this->filters;
    }

    public function addFilter(ProductFilter $filter)
    {
        if ($filter->isRelevant()) {
            $this->filters[] = $filter;
            $type = $filter->getType();
            if (!isset($this->filtersIndex[$type])) {
                $this->filtersIndex[$type] = [];
            }
            $this->filtersIndex[$type][] = $filter;
            $this->filtersIdIndex[$filter->getId()] = $filter;
        }
    }

    /**
     * @param $type
     * @return ProductFilter[]
     */
    public function getFiltersByType($type)
    {
        $this->getFilters();
        return isset($this->filtersIndex[$type]) ? $this->filtersIndex[$type] : [];
    }

    public function getSortingOptions()
    {
        if ($this->sortingOptions === null) {
            $this->sortingOptions = [];
            $activeSortArgument = $this->getFilterSort();
            $activeOrderArgument = $this->getFilterOrder();
            $translationsManager = $this->getService('translationsManager');
            if ($this->isFieldSortable('manual')) {
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'manual',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_manual'),
                    'value' => 'manual;asc',
                ];
            }
            if ($this->isFieldSortable('price')) {
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'price' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_price'),
                    'value' => 'price;asc',
                ];
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'price' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_price_desc'),
                    'value' => 'price;desc',
                ];
            }
            if ($this->isFieldSortable('title')) {
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'title' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_title'),
                    'value' => 'title;asc',
                ];
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'title' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_title_desc'),
                    'value' => 'title;desc',
                ];
            }
            if ($this->isFieldSortable('date')) {
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'date' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_date'),
                    'value' => 'date;asc',
                ];
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'date' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_date_desc'),
                    'value' => 'date;desc',
                ];
            }
            if ($this->isFieldSortable('brand')) {
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'brand' && $activeOrderArgument == 'asc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_brand'),
                    'value' => 'brand;asc',
                ];
                $this->sortingOptions[] = [
                    'active' => $activeSortArgument == 'brand' && $activeOrderArgument == 'desc',
                    'label' => $translationsManager->getTranslationByName('products.sort_by_brand_desc'),
                    'value' => 'brand;desc',
                ];
            }
        }
        return $this->sortingOptions;
    }

    public function getFiltersData()
    {
        $data = [];
        foreach ($this->getFilters() as $filter) {
            $data[] = $filter->getData();
        }
        return $data;
    }


    abstract public function isFilterableByType($filterType);

    abstract public function isFilterable();

    abstract public function getFilterSort();

    abstract public function getFilterOrder();

    abstract public function getFilteredUrl();

    abstract public function isFieldSortable($fieldType);

    /**
     * @return ProductsListElement
     */
    abstract public function getProductsListElement();

    abstract public function getParameterSelectionsForFiltering();

}