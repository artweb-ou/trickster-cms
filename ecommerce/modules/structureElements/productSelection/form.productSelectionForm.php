<?php

class ProductSelectionFormStructure extends ElementForm
{
    protected $formClass = 'product_selection_form';
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'hint' => [
            'type' => 'input.multi_language_content',
        ],
        'primary' => [
            'type' => 'select.index',
            'options' => [
                0 => 'detailedview',
                1 => 'allviews',
                2 => 'onlyoption',
            ],
            'class' => 'dropdown_placeholder',
            'translationGroup' => 'productparameter',
        ],
        'formFilterableCategoriesIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedFilterableCategories',
            'class' => 'product_selection_form_filterable_categories_select',
        ],
        'code' => [
            'type' => 'input.text',
        ],
        'type' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'selection_standard',
                'color' => 'selection_color',
            ],
        ],
        'option' => [
            'type' => 'input.checkbox',
        ],
        'controlType' => [
            'type' => 'select.index',
            'options' => [
                'dropdown' => 'control_type_dropdown',
                'radios' => 'control_type_radios',
            ],
            'translationGroup' => 'product_selection',
        ],
        'influential' => [
            'type' => 'input.checkbox',
        ],
        'categoriesIds' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'categoriesList',
            'class' => 'select_multiple_categories',
        ],
    ];

    protected $additionalContent = 'component.block.product_selection';
}