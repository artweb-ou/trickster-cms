<?php

class SharedMultiLanguageSeoStructure extends ElementForm
{
    protected $structure = [
        'structureName' => [
            'type' => 'input.text',
        ],
        'metaTitle' => [
            'type' => 'input.multi_language_text',
        ],
        'h1' => [
            'type' => 'input.multi_language_text',
        ],
        'metaDescription' => [
            'type' => 'input.multi_language_textarea',
        ],
        'canonicalUrl' => [
            'type' => 'input.text',
        ],
        'metaDenyIndex' => [
            'type' => 'input.checkbox',
        ],
    ];


    public function getTranslationGroup()
    {
        return 'seo';
    }
}