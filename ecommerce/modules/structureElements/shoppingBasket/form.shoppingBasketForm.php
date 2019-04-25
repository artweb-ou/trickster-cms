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
        // "Add To Basket" button action
        // "Lisa ostukorvi" nuppu toiming
        // Действие кнопки «Добавить в корзину»
        'addToBasketButtonAction' => [ // ALTER TABLE `engine_module_shoppingbasket` ADD `addToBasketButtonAction` INT(2) NOT NULL DEFAULT '0' AFTER `conditionsLink`;
            'type' => 'select.index',
            'options' => [
                '0' => 'none',
                '1' => 'tooltip',
                '2' => 'modal',
            ],
        ],
    ];

    protected $additionalContent = 'shared.contentlist_singlepage';
}