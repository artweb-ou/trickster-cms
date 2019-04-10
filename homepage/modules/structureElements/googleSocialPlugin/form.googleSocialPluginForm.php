<?php

class GoogleSocialPluginFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'icon' => [
            'type' => 'input.image',
        ],
        'social' => [
            'type' => 'input.social_multi_language',
        ],
    ];

}