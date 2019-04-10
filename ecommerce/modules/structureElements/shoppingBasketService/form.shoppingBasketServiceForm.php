<?php

class ShoppingBasketServiceFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'price' => [
            'type' => 'input.text',
        ],
    ];

}