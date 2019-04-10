<?php

class ProductParameterFormStructure extends ElementForm
{
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
        'single' => [
            'type' => 'input.checkbox',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'categoriesIds' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'categoriesList',
            'class' => 'select_multiple_categories',
        ],
    ];

}