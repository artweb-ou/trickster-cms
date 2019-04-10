<?php

class ProductionFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'introduction' => [
            'type' => 'input.html',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'file' => [
            'type' => 'input.file',
        ],
        'galleries' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'galleriesList',

        ],
        'feedbackId' => [
            'type' => 'select.element',
            'property' => 'feedbackFormsList',
            'defaultRequired' => true,
        ],
        'structureRole' => [
            'type' => 'select.array',
            'options' => ['hybrid', 'content', 'container'],
            'translationGroup' => 'menulogic',
        ],
    ];

}