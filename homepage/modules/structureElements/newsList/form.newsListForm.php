<?php

class NewsListFormStructure extends ElementForm
{
    protected $formClass = "";
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
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
        'displayAmount' => [
            'type' => 'input.amount',
        ],
        'itemsOnPage' => [
            'type' => 'input.amount',
        ],
        'archiveEnabled' => [
            'type' => 'input.checkbox',
        ],
    ];

}