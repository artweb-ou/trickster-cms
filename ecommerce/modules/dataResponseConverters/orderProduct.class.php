<?php

class orderProductDataResponseConverter extends StructuredDataResponseConverter
{
    protected $defaultPreset = 'api';

    protected function getRelationStructure()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'searchTitle' => 'getSearchTitle',
            'url' => 'getUrl',
            'structureType' => 'structureType',
            'variation' => 'variation',
            'amount' => 'amount',
            'oldPrice' => 'oldPrice',
            'price' => 'price',
            'category' => 'getCategoryTitle',
            'category_ga' => 'getCategoryDefaultTitle',
            'title_ga' => 'title_dl',
            'variation_ga' => 'variation_dl',
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

                'variation',
                'amount',
                'oldPrice',
                'price',
                'category',
                'category_ga',
                'title_ga',
                'variation_ga',
            ],
            'search' => [
                'id',
                'searchTitle',
                'url',
                'structureType',
            ],
        ];
    }
}