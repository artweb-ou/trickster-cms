<?php

class CategoryFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
        'unit' => [
            'type' => 'input.multi_language_text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'introduction' => [
            'type' => 'input.multi_language_content',
        ],
        'content' => [
            'type' => 'input.multi_language_content',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
    ];

}