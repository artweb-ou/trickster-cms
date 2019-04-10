<?php

class ProductVariationFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'color' => [
            'type' => 'input.text',
        ],
    ];

}