<?php

class ShopCatalogueControlsFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'class' => '',
        ],
    ];
    protected $controls = 'controls';
}