<?php

class SelectedDiscountsFormStructure extends ElementForm
{
    protected $formClass = 'selecteddiscounts_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'receivedDiscountsIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getAvailableDiscounts',
            'class' => 'selecteddiscounts_connected_discounts_select',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
            'translationGroup' => 'shared',
        ],
    ];

}