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
            'products' => 'getProductsData',
            'filters' => 'getFiltersData',
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
                'filterLimit',
            ],
        ];
    }
}