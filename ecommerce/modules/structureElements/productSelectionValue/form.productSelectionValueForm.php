<?php

class ProductSelectionValueFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'hint' => [
            'type' => 'input.multi_language_content',
        ],
        'importKeywords' => [
            'type' => 'input.text',
        ],
        'excludeImportKeywords' => [
            'type' => 'input.text',
        ],
        'value' => [
            'type' => 'input.text',
        ],
        'price' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
    ];

}