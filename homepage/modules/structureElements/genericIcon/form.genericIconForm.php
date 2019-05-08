<?php

class GenericIconFormStructure extends ElementForm
{
    protected $formClass = 'genericicon_form';
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'image' => [
            'type' => 'input.multi_language_image',
        ],
        'iconWidth' => [
            'type' => 'input.multi_language_text',
        ],
        'startDate' => [
            'type' => 'input.date',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
        'days' => [
            'type' => 'input.text',
        ],
        'products' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedProducts',
            'class' => 'genericicon_form_productselect',
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedCategoriesInfo',
            'class' => 'genericicon_form_categoryselect',
        ],
        'brands' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedBrands',
            'class' => 'genericicon_form_brandselect',
        ],
    ];

}