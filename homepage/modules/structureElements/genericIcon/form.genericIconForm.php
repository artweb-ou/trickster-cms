<?php

class GenericIconFormStructure extends ElementForm
{
    protected $formClass = 'genericicon_form';
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'image' => [
            'type' => 'input.multi_language_image',
            'class' => 'genericicon_icon_image',
        ],
        'iconWidth' => [
            'type' => 'input.multi_language_text',
        ],
        'iconLocation' => [
            'type' => 'select.index',
            'options' => [
                'loc_top_left',
                'loc_top_right',
                'loc_bottom_left',
                'loc_bottom_right',
            ],
         //   'translationGroup' => 'order', //'translationGroup' => 'admintranslation',
        ],
        'iconRole' => [
            'type' => 'select.index',
            'options' => [
                'role_default',
                'role_date',
                'role_general_discount',
                'role_availability',
                'role_by_parameter',
            ],
         //   'translationGroup' => 'order', //'translationGroup' => 'admintranslation',
        ],
        'startDate' => [
            'type' => 'input.date',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
        'days' => [
            'type' => 'input.text',
        ],
        'products' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedProducts',
            'class' => 'genericicon_form_productselect',
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedCategoriesInfo',
            'class' => 'genericicon_form_categoryselect',
        ],
        'brands' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedBrands',
            'class' => 'genericicon_form_brandselect',
        ],
    ];

}