<?php

class SharedIconStructure extends ElementForm
{
    protected $preset = '';
    protected $structure = [
        'connectedIconIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getGenericIconList',
        ],
        'addIcon'  => [
            'type'             => 'show.heading',
            'translationGrupp' => 'shared'
        ],
        'newIcon' => [
            'type' => 'button.createNewElement'
        ]
    ];
    protected $additionalContent = 'shared.contentlist.tpl';
}