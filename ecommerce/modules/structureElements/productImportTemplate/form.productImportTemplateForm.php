<?php

class ProductImportTemplateFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'importOrigin' => [
            'type' => 'input.text',
        ],
        'priceAdjustment' => [
            'type' => 'input.text',
        ],
        'delimiter' => [
            'type' => 'input.text',
        ],
        'ignoreFirstRow' => [
            'type' => 'input.checkbox',
        ],
    ];

}