<?php

class googleSocialPluginElement extends socialPluginElement
{
    public function getSpecialFields()
    {
        return [
            'clientId' => [
                'format' => 'text',
                'multiLanguage' => false,
            ],
            'clientSecret' => [
                'format' => 'text',
                'multiLanguage' => false,
            ],
        ];
    }

    public function getApiClass()
    {
        return 'googleSocialNetworkAdapter';
    }
}