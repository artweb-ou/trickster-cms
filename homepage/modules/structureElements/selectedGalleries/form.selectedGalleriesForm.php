<?php

class SelectedGalleriesFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'galleries' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'galleriesInfo',

        ],
    ];

}