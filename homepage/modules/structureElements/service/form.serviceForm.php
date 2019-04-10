<?php

class ServiceFormStructure extends ElementForm
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
        'galleries' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'galleries',
            'class' => 'service_form_galleryselector',
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