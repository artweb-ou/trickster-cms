<?php

class CategoryTextsStructure extends ElementForm
{
    protected $formClass = 'product_form';
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'introduction' => [
            'type' => 'input.multi_language_content',
        ],
        'content' => [
            'type' => 'input.multi_language_content',
        ]
    ];

}