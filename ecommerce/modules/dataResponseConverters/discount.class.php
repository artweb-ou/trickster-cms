<?php

class discountDataResponseConverter extends StructuredDataResponseConverter
{
    protected $defaultPreset = 'api';

    protected function getRelationStructure()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'searchTitle' => 'title',
            'url' => 'getUrl',
            'structureType' => 'structureType',
            'image' => 'image',
            'icon' => 'icon',
            'link' => 'link',
            'content' => 'content',
            'dateCreated' => function ($element) {
                return $element->getValue('dateCreated');
            },
            'dateModified' => function ($element) {
                return $element->getValue('dateModified');
            },
            'productsLayout' => function ($element) {
                return $element->getCurrentLayout('productsLayout');
            },
            'products' => 'getProductsData',
            'filters' => 'getFiltersData',
            'filterDiscountIds' => 'getFilterDiscountIds',
            'filterBrandIds' => 'getFilterBrandIds',
            'filterCategoryIds' => 'getFilterCategoryIds',
            'filterActiveParametersInfo' => 'getFilterActiveParametersInfo',
            'filterAvailability' => 'getFilterAvailability',
            'filterPrice' => 'getFilterPrice',
            'filteredProductsAmount' => 'getFilteredProductsAmount',
            'filterLimit' => 'getFilterLimit',
            'filterOrder' => 'getFilterOrder',
            'filterSort' => 'getFilterSort',
            'affectsPublicUrl' => 'affectsPublicUrl',
            'currentPage' => function (ProductsListElement $element) {
                return $element->getProductsPager()->getCurrentPage();
            },
        ];
    }

    protected function getPresetsStructure()
    {
        return [
            'api' => [
                'id',
                'title',
                'dateCreated',
                'dateModified',
                'url',
                'image',
                'icon',
                'link',
                'content',
            ],
            'search' => [
                'id',
                'searchTitle',
                'url',
                'structureType',
            ],
            'list' => [
                'id',
                'title',
                'url',
                'products',
                'filters',
                'filteredProductsAmount',
                'filterDiscountIds',
                'filterBrandIds',
                'filterCategoryIds',
                'filterActiveParametersInfo',
                'filterAvailability',
                'filterPrice',
                'filterLimit',
                'filterOrder',
                'filterSort',
                'currentPage',
                'productsLayout',
                'affectsPublicUrl',
            ],
        ];
    }
}