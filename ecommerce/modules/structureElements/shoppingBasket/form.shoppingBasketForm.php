<?php

class ShoppingBasketFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'destination' => [
            'type' => 'input.email',
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
        'paymentSuccessfulText' => [
            'type' => 'input.html',
        ],
        'paymentInvoiceText' => [
            'type' => 'input.html',
        ],
        'paymentQueryText' => [
            'type' => 'input.html',
        ],
        'paymentFailedText' => [
            'type' => 'input.html',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
        'conditionsLink' => [
            'type' => 'input.text',
        ],
        'addToBasketButtonAction' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'action_none',
                '1' => 'action_tooltip',
                '2' => 'action_modal',
            ],
        ],
    ];

    protected $additionalContent = 'shared.contentlist_singlepage.tpl';
}