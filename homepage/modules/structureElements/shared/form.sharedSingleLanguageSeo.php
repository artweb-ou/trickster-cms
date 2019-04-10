<?php

class SharedSingleLanguageSeoStructure extends ElementForm
{
    protected $structure = [
        'structureName' => [
            'type' => 'input.text',
        ],
        'metaTitle' => [
            'type' => 'input.text',
        ],
        'h1' => [
            'type' => 'input.text',
        ],
        'metaDescription' => [
            'type' => 'input.textarea',
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