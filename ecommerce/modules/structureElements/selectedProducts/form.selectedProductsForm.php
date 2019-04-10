<?php

class SelectedProductsFormStructure extends ElementForm
{
    protected $formClass = 'selectedproducts_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'selectionType' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'selectiontype_auto',
                '1' => 'selectiontype_manual',
            ],
            'translationGroup' => 'field',
            'class' => 'selectedproducts_type_select',
        ],
        'products' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'productsInfo',
            'class' => 'selectedproducts_connectedproducts',
            'trClass' => 'manual_selection_related',
        ],
        'autoSelectionType' => [
            'type' => 'select.index',
            'options' => [
                '0' => 'autoselect_newest',
                '1' => 'autoselect_popular',
                '2' => 'autoselect_recently_purchased',
                '3' => 'autoselect_discounted',
                '4' => 'autoselect_available',
            ],
            'class' => 'dropdown_placeholder',
            'trClass' => 'auto_selection_related',
        ],
        'amount' => [
            'type' => 'input.text',
            'trClass' => 'auto_selection_related',
        ],
        'productSelectionIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getProductSelectionParameters',
            'class' => 'selectedproducts_form_parameters',
            'trClass' => 'auto_selection_related',
        ],
        'categoriesIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedCategories',
            'class' => 'selectedproducts_categoryselect',
            'trClass' => 'auto_selection_related',
        ],
        'brandsIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedBrands',
            'class' => 'selectedproducts_brandselect',
            'trClass' => 'auto_selection_related',

        ],
        'discountsIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedDiscounts',
            'class' => 'selectedproducts_discountselect',
            'trClass' => 'auto_selection_related',
        ],
        'iconIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedIcons',
            'class' => 'selectedproducts_iconselect',
            'trClass' => 'auto_selection_related',

        ],
        'priceSortingEnabled' => [
            'type' => 'input.checkbox',
        ],
        'nameSortingEnabled' => [
            'type' => 'input.checkbox',
        ],
        'dateSortingEnabled' => [
            'type' => 'input.checkbox',
        ],
        'amountOnPageEnabled' => [
            'type' => 'input.checkbox',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',

        ],
    ];
}