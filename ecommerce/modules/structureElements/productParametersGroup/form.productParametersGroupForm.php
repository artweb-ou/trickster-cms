<?php

class ProductParametersGroupFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'isMinimized' => [
            'type' => 'input.checkbox',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
    ];

}