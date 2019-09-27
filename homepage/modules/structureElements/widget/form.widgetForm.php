<?php

class WidgetFormStructure extends ElementForm
{
    protected $structure = [
        'title'   => [
            'type' => 'input.text',
        ],
        'hideTitle'  => [
            'type' => 'input.checkbox',
            'translationGroup' => 'shared',
        ],
        'image'   => [
            'type'     => 'input.image',
            'preset'   => 'adminImage',
            'filename' => 'originalName',
        ],
        'image2'   => [
            'type'     => 'input.image',
            'preset'   => 'adminImage',
            'filename' => 'image2OriginalName',
        ],
        'content' => [
            'type' => 'input.html'
        ],
        'marker'    => [
            'type' => 'input.text'
        ],
        'code'    => [
            'type' => 'input.textarea'
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

}