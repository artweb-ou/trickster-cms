<?php

class categoryDataResponseConverter extends StructuredDataResponseConverter
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
            'introductionText' => function ($element, $scope) {
                return $scope->htmlToPlainText($element->introduction);
            },
            'contentText' => function ($element, $scope) {
                return $scope->htmlToPlainText($element->content);
            },
            'searchAmount' => 'getProductsListBaseAmount',
            'introduction' => 'introduction',
            'content' => 'content',
            'image' => 'image',
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
            ],
            'search' => [
                'id',
                'searchTitle',
                'url',
                'structureType',
                'introductionText',
                'searchAmount',
            ],
        ];
    }
}