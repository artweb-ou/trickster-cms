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
        'decimals' => [
            'type' => 'input.text',
        ],
        'decPoint' => [
            'type' => 'input.text',
        ],
        'thousandsSep' => [
            'type' => 'input.text',
        ],
    ];

}