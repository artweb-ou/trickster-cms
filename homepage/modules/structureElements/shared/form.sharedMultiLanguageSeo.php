<?php

class SharedMultiLanguageSeoStructure extends ElementForm
{
    protected $structure = [
        'structureName' => [
            'type' => 'input.text',
            'translationGroup' => 'seo',
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
            'translationGroup' => 'seo',
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