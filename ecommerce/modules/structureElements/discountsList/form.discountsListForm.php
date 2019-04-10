<?php

class DiscountsListFormStructure extends ElementForm
{
    protected $formClass = 'discountslist';
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
        'discounts' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'discountsList',
            'class' => 'discountslist_discounts_select',

        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
        'structureRole' => [
            'type' => 'select.array',
            'options' => ['hybrid', 'content', 'container'],
            'translationGroup' => 'menulogic',
        ],
    ];

}