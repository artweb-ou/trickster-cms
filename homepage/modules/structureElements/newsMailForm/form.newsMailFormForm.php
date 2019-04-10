<?php

class NewsMailFormFormStructure extends ElementForm
{
    protected $structure = [
        'title'        => [
            'type' => 'input.text',
        ],
        'description'  => [
            'type' => 'input.html',
        ],
        'displayMenus' => [
            'type'      => 'select.universal_options_multiple',
            'method'    => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

}