<?php

class SharedFormStructure extends ElementForm
{
    protected $structure = [
        'title'        => [
            'type' => 'input.multi_language_text',
        ],
        'marker'       => [
            'type' => 'input.text',
        ],
        'displayMenus' => [
            'type'      => 'select.universal_options_multiple',
            'method'    => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ]
    ];


    public function getTranslationGroup()
    {
        return 'shared';
    }
}