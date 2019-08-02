<?php

class SharedFormStructure extends ElementForm
{
    protected $structure = [
        'title'        => [
            'type' => 'input.multi_language_text',
            'translationsGroup' => 'shared',
        ],
        'marker'       => [
            'type' => 'input.text',
            'translationsGroup' => 'shared',
        ],
        'displayMenus' => [
            'type'      => 'select.universal_options_multiple',
            'method'    => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
            'translationsGroup' => 'shared',
        ]
    ];


    public function getTranslationGroup()
    {
        return 'shared';
    }
}