<?php

class facebookSocialPluginElement extends socialPluginElement
{
    public function getSpecialFields()
    {
        return [
            'appId' => [
                'format' => 'text',
                'multiLanguage' => false,
            ],
            'appKey' => [
                'format' => 'text',
                'multiLanguage' => false,
            ],
        ];
    }

    public function getApiClass()
    {
        return 'facebookSocialNetworkAdapter';
    }

//    public function makePost() {
//
//    }
}