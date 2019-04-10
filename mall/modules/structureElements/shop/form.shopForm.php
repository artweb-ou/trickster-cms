<?php

class ShopFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'structureName' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'photo' => [
            'type' => 'input.image',
        ],
        'rooms' => [
            'type' => 'select.shop_rooms_categories',
            'property' => 'floorsList',
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'categoriesList',
            'class' => '',
        ],
        'campaigns' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'campaignsList',
            'class' => '',
        ],
        'open_hours' => [
            'type' => 'input.opening_hours_form_section',
        ],
    ];
    protected $controls = 'controls';
}