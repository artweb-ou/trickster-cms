<?php

class ProductImportTemplateColumnFormStructure extends ElementForm
{
    protected $formClass = 'productimporttemplatecolumn_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'mandatory' => [
            'type' => 'input.checkbox',
        ],
        'columnNumber' => [
            'type' => 'input.text',
        ],
        'productVariable' => [
            'type' => 'select.array',
            'options' => [
                'importId',
                'code',
                'title',
                'introduction',
                'content',
                'price',
                'oldPrice',
                'images',
                'pdf',
                'quantity',
                'minimumOrder',
                'categoryCode',
                'categoryCode0',
                'categoryCode1',
                'categoryCode2',
                'categoryCode3',
                'categoryCode4',
                'brand',
                'parameter',
            ],
        ],
        'productParameterId' => [
            'type' => 'input.search_product',
        ],
    ];

}