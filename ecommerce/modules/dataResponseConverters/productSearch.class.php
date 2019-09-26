<?php

class productSearchDataResponseConverter extends StructuredDataResponseConverter
{
    protected $defaultPreset = 'api';

    protected function getRelationStructure()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'url' => 'URL',
            'structureType' => 'structureType',
            'dateCreated' => function ($element) {
                return $element->getValue('dateCreated');
            },
            'dateModified' => function ($element) {
                return $element->getValue('dateModified');
            },
            'checkboxesForParameters' => 'checkboxesForParameters',
            'pricePresets' => 'pricePresets',
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
            ],
            'list' => [
                'id',
                'title',
                'url',
                'checkboxesForParameters',
                'pricePresets',
                'filters',
            ],
        ];
    }
}