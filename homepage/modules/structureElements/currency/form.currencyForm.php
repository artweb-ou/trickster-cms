<?php

class CurrencyFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'code' => [
            'type' => 'input.text',
        ],
        'symbol' => [
            'type' => 'input.text',
        ],
        'rate' => [
            'type' => 'input.text',
        ],
    ];

}