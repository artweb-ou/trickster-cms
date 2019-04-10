<?php

class IconFormStructure extends ElementForm
{
    protected $formClass = 'product_form';
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
    ];
    protected $controls = 'controls';
}