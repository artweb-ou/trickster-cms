<?php

class LanguageFormStructure extends ElementForm
{
    protected $structure = [
        'iso6393' => [
            'type' => 'input.text',
        ],
        'title' => [
            'type' => 'input.text',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
        'image' => [
            'type' => 'input.image',
            'filename' => 'originalName',
        ],
        'logoImage' => [
            'type' => 'input.image',
            'filename' => 'logoImageOriginalName',
        ],
        'promoText' => [
            'type' => 'input.text',
        ],
        'backgroundImage' => [
            'type' => 'input.image',
            'filename' => 'backgroundImageOriginalName',
        ],
    ];
}