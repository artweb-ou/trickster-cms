<?php

class GalleryFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'hideTitle' => [
            'type' => 'input.checkbox',
            'translationGroup' => 'shared',
        ],
//        'service' => [
//            'type' => 'select.array',
//        ],
        'content' => [
            'type' => 'input.html',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

}