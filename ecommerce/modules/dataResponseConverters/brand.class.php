<?php

class brandDataResponseConverter extends StructuredDataResponseConverter
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
            'dateCreated' => function ($element) {
                return $element->getValue('dateCreated');
            },
            'dateModified' => function ($element) {
                return $element->getValue('dateModified');
            },
            'introduction' => 'introduction',
            'content' => 'content',
            'image' => 'image',
            'imageUrl' => function ($element) {
                return controller::getInstance()->baseURL . "image/type:brandWidgetItem/id:" . $element->image . "/filename:" . $element->originalName;
            },
            'products' => 'getProductsData',
            'filters' => 'getFiltersData',
            'filteredProductsAmount' => 'getFilteredProductsAmount',
            'filterLimit' => 'getFilterLimit',
            'filterOrder' => 'getFilterOrder',
            'filterSort' => 'getFilterSort',
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
                'introduction',
                'content',
                'image',
                'imageUrl',
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
                'filterLimit',
                'filterOrder',
                'filterSort',
                'currentPage',
                'productsLayout',
            ],
        ];
    }
}