<?php

class ProductTextsStructure extends ElementForm
{
    protected $formClass = 'product_form';
    protected $structure = [
        'introduction' => [
            'type' => 'input.multi_language_content',
        ],
        'content' => [
            'type' => 'input.multi_language_content',
        ]
    ];
    protected $additionalContent = 'shared.contentlist.tpl';
}