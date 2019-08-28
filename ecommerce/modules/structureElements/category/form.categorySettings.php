<?php

class CategorySettingsStructure extends ElementForm
{
    protected $formClass = 'category_form';
    protected $structure = [
        'unit' => [
            'type' => 'input.multi_language_text',
        ],
        'parameters' => [
            'type' => 'select.parameters_group',
        ],
        'defaultOrder' => [
            'type' => 'select.index',
            'options' => [
                'manual' => 'defaultorder',
                'price' => 'pricesorting',
                'price;desc' => 'pricesorting_descending',
                'title' => 'titlesorting',
                'title;desc' => 'titlesorting_descending',
                'brand' => 'brandsorting',
                'brand;desc' => 'brandsorting_descending',
                'date' => 'datesorting',
                'date;desc' => 'datesorting_descending',
            ],
            'translationGroup' => 'category',
        ],
        'manualSortingEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'priceSortingEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'nameSortingEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'dateSortingEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'amountOnPageEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'brandFilterEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'parameterFilterEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'discountFilterEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'availabilityFilterEnabled' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'sameasparent',
                '1' => 'enabled',
                '2' => 'disabled',
            ],
            'translationGroup' => 'category',
        ],
        'productCataloguesIds' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'productCatalogues',
            'defaultRequired' => true,
        ],
        'feedbackId' => [
            'type' => 'multi_language_feedback',
            'property' => 'feedbackFormsList',
            'defaultRequired' => true,
        ],
        'parentCategoriesIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getParentCategoriesList',
            'defaultRequired' => true,
        ],
    ];

}