<?php

class CollectionsListFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'columns' => [
            'type' => 'select.index',
            'options' => [
                'left' => 'columns_left',
                'right' => 'columns_right',
                'both' => 'columns_both',
                'none' => 'columns_none',
            ],
            'translationGroup' => 'selector',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'connectAll' => [
            'type' => 'input.checkbox',
        ],
        'brands' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getCollectionsInfo',
        ],
    ];

}