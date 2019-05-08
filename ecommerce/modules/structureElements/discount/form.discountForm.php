<?php

class DiscountFormStructure extends ElementForm
{
    protected $formClass = 'discount_form';
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'link' => [
            'type' => 'input.text',
        ],
        'content' => [
            'type' => 'input.multi_language_content',
        ],
        'reference' => [
            'type' => 'input.multi_language_content',
        ],
        'code' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.multi_language_image',
        ],
        'icon' => [
            'type' => 'input.multi_language_image',
        ],
        'iconWidth' => [
            'type' => 'input.multi_language_text',
        ],
        'showInBasket' => [
            'type' => 'input.checkbox',
        ],
        'basketText' => [
            'type' => 'input.text',
        ],
        'displayProductsInBasket' => [
            'type' => 'input.checkbox',
        ],
        'conditions' => [
            'type' => 'show.heading',
        ],
        'promoCode' => [
            'type' => 'input.text',
        ],
        'startDate' => [
            'type' => 'input.date',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
        'conditionUserGroupId' => [
            'type' => 'select.element',
            'method' => 'getUserGroups',
            'defaultRequired' => true,
        ],
        'conditionPrice' => [
            'type' => 'input.text',
        ],
        'conditionPriceMax' => [
            'type' => 'input.text',
        ],
        'groupBehaviour' => [
            'type' => 'select.index',
            'options' => [
                'useSmallest' => 'use_smallest',
                'dominateSmaller' => 'dominate_smaller',
                'cooperate' => 'cooperate',
            ],
            'translationGroup' => 'discount',
        ],
        'targeted_products' => [
            'type' => 'show.heading',
        ],
        'targetAllProducts' => [
            'type' => 'input.checkbox',
        ],
        'products' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedProducts',
            'class' => 'discount_form_productselect',
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedCategoriesInfo',
            'class' => 'discount_form_categoryselect',
        ],
        'brands' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedBrands',
            'class' => 'discount_form_brandselect',
        ],
        'price_changes' => [
            'type' => 'show.heading',
        ],
        'productDiscount' => [
            'type' => 'input.text',
        ],
        'fixedPrice' => [
            'type' => 'input.text',
        ],
        'deliveryTypes' => [
            'type' => 'input.multi_text',
        ],
    ];

}