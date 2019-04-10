<?php

class ShopCategoryFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'color' => [
            'type' => 'input.color',
        ],
        'image' => [
            'type' => 'input.image',
        ],
    ];
    protected $controls = 'controls';
}