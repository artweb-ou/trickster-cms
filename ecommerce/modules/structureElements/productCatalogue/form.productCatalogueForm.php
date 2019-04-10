<?php

class ProductCatalogueFormStructure extends ElementForm
{
    protected $formClass = 'productcatalogue_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'columns' => [
            'type' => 'select.index',
            'options' => [
                'left' => 'columns_left',
                'right' => 'columns_right',
                'both' => 'columns_both',
                'none' => 'columns_none',
            ],
            'translationGroup' => 'selector',
        ],
        'categorized' => [
            'type' => 'input.checkbox',
        ],
        'categories' => [
            'type'     => 'select.universal_options_multiple',
            'property' => 'categoriesList',
            'trClass'  => 'productcatalogue_form_check_row_categories'
        ],
        'structureRole' => [
            'type' => 'select.array',
            'options' => ['hybrid', 'content', 'container'],
            'translationGroup' => 'menulogic',
        ],
    ];

}