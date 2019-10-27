<?php

class PersonnelFormStructure extends ElementForm
{
    protected $structure = [
        'image' => [
            'type' => 'input.image',
        ],
        'status' => [
            'type' => 'input.text',
        ],
        'title' => [
            'type' => 'input.text',
        ],
        'position' => [
            'type' => 'input.text',
        ],
        'email' => [
            'type' => 'input.text',
        ],
        'phone' => [
            'type' => 'input.text',
        ],
        'mobilePhone' => [
            'type' => 'input.text',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
            'translationGroup' => 'shared',
        ],
    ];

}