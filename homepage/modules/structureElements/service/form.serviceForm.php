<?php

class ServiceFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'icon' => [
            'type' => 'input.image',
            'filename' => 'iconOriginalName',
        ],
        'introduction' => [
            'type' => 'input.html',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'image' => [
            'type' => 'input.image',
            'filename' => 'originalName',
        ],
        'link_text_1' => [
            'type' => 'input.text',
        ],
        'link_1' => [
            'type' => 'input.text',
        ],
        'link_text_2' => [
            'type' => 'input.text',
        ],
        'link_2' => [
            'type' => 'input.text',
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
            'class' => 'service_mode_select',
            'options' => ['hybrid', 'content', 'container'],
            'translationGroup' => 'menulogic',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

}