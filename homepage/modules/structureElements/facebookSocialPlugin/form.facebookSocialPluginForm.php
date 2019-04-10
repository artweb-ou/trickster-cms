<?php

class FacebookSocialPluginFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'icon' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
        ],
        'social' => [
            'type' => 'input.social_multi_language',
        ],
    ];


    public function getTranslationGroup()
    {
        return 'socialplugin';
    }
}