<?php

class BannerCategoryFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'limit' => [
            'type' => 'input.text',
        ],
        'marker' => [
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