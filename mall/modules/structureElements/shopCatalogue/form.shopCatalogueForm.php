<?php

class ShopCatalogueFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'columns' => [
            'type' => 'select.universal_options',
            'options' => ['left', 'right', 'both', 'none',],
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'categoriesList',
            'class' => '',
        ],
    ];
    protected $controls = 'controls';
}