<?php

class twitterSocialPluginElement extends socialPluginElement
{
    protected $allowedTypes = [

    ];
    protected $pages;

    public function getSpecialFields()
    {
        return [
            'appId'  => [
                'format'        => 'text',
                'multiLanguage' => false,
            ],
            'appKey' => [
                'format'        => 'text',
                'multiLanguage' => false,
            ],
        ];
    }

    protected function getTabsList()
    {
        return [
            'showForm',
        ];
    }

    public function getApiClass()
    {
        return 'twitterSocialNetworkAdapter';
    }
}