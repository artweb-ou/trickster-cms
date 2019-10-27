<?php

class ProductSearchFormStructure extends ElementForm
{
    protected $formClass = 'productsearch_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'pageDependent' => [
            'type' => 'input.checkbox',
            'class' => 'productsearch_form_dependent_checkbox'
        ],
        'catalogueFilterId' => [
            'type' => 'select.element',
            'property' => 'productCataloguesInfo',
        ],
        'filterCategory' => [
            'type' => 'input.checkbox',
        ],
        'filterBrand' => [
            'type' => 'input.checkbox',
        ],
        'filterDiscount' => [
            'type' => 'input.checkbox',
        ],
        'availabilityFilterEnabled' => [
            'type' => 'input.checkbox',
        ],
        'sortingEnabled' => [
            'type' => 'input.checkbox',
        ],
        'parametersIds' => [
            'trClass' => 'productsearch_parameters',
            'type' => 'select.universal_options_multiple',
            'class' => 'productsearch_form_parameters',
            'method' => 'getConnectedParameters'
        ],
        'checkboxesForParameters' => [
            'type' => 'input.checkbox',
        ],
        'filterPrice' => [
            'class' => 'productsearch_form_price_checkbox',
            'type' => 'input.checkbox',
        ],
        'pricePresets' => [
            'trClass' => 'productsearch_form_price_presets',
            'type' => 'input.checkbox',
        ],
        'priceInterval' => [
            'trClass' => 'productsearch_form_price_interval',
            'type' => 'input.text',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
            'translationGroup' => 'shared',
        ],
    ];

}