<?php

class CategoryFormStructure extends ElementForm
{
    protected $structure = [
        'image' => [
            'type' => 'input.image',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
    ];

}