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
        'markerLogic' => [
            'type' => 'select.index',
            'options' => [
                0=>'hide',
                1=>'marker',
                2=>'marker_title_mouseover',
                3=>'show_both'
            ],
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