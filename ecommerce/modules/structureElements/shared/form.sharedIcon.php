<?php

class SharedIconStructure extends ElementForm
{
    protected $preset = '';
    protected $structure = [
        'connectedIconIds' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'iconsList',
        ],
        'addIcon'  => [
            'type'             => 'show.heading',
            'translationGrupp' => 'shared'
        ],
        'newIcon' => [
            'type' => 'button.createNewElement'
        ]
    ];
}