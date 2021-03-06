<?php

class CategorySeoStructure extends ElementForm
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
        'products_seo' => [
            'type' => 'show.heading',
        ],
        'metaTitleTemplate' => [
            'type' => 'input.multi_language_text',
        ],
        'metaH1Template' => [
            'type' => 'input.multi_language_text',
        ],
        'metaSubTitleTemplate' => [
            'type' => 'input.multi_language_text',
        ],
        'metaDescriptionTemplate' => [
            'type' => 'input.multi_language_textarea',
        ],
    ];


    public function getTranslationGroup()
    {
        return 'seo';
    }
}