<?php

class ProductGalleryFormStructure extends ElementForm
{
    protected $structure = [
        'title'                 => [
            'type' => 'input.text',
        ],
        'description'           => [
            'type' => 'input.html',
        ],
        'structureRole' => [
            'type' => 'select.array',
            'options' => ['hybrid', 'content', 'container'],
            'translationGroup' => 'menulogic',
        ],
        'popup'                 => [
            'type' => 'input.checkbox',
        ],
        'showConnectedProducts' => [
            'type' => 'input.checkbox',
        ],
        'displayMenus'          => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

}