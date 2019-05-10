<?php

class GenericIconFormStructure extends ElementForm
{

    protected $formClass = 'genericicon_form';
    protected $structure = [];
    protected $structure_total_top = [
        'title' => [
            'type' => 'input.multi_language_text',
            'class' => 'genericicon_title',
        ],
        'image' => [
            'type' => 'input.multi_language_image',
            'class' => 'genericicon_icon',
        ],
        'iconWidth' => [
            'type' => 'input.multi_language_text',
            'inputType' => 'number',
        ],
        'iconLocation' => [
            'type' => 'select.index',
            'options' => [
                1 => 'loc_top_left',
                2 => 'loc_top_right',
                3 => 'loc_bottom_left',
                4 => 'loc_bottom_right',
            ],
         //   'translationGroup' => 'order', //'translationGroup' => 'admintranslation',
        ],
        'iconRole' => [
            'type' => 'select.index',
            'options' => [
                1 => 'role_default',
                2 => 'role_date',
                3 => 'role_general_discount',
                4 => 'role_availability',
                5 => 'role_by_parameter',
            ],
         //   'translationGroup' => 'order', //'translationGroup' => 'admintranslation',
        ],
    ];
    protected $structure_total_bottom = [
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

    protected $structure_role_date = [
        'startDate' => [
            'type' => 'input.date',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
        'days' => [
            'type' => 'input.text',
        ],
    ];

    protected $structure_role_availability = [
        'iconProductAvail' => [
            'type' => 'select.serialized',
            'method' => 'getProductsAvailabilityOptions',
            'translationGroup' => 'product',
            'class' => 'select_simple',
        ],
    ];

    protected $structure_role_by_parameter = [
        'parameters' => [
            //    'type' => 'select.parameters_group',
                       'type' => 'select.universal_options_multiple',
//            'method' => 'getConnectedParameters',
            'method' => 'getProductSelectionParameters',
            //            'method' => 'getConnectedParametersIds',
            'class' => 'genericicon_form_parameterselect',
        ],
    ];

//'parametersIds' => [
//'trClass' => 'productsearch_parameters',
//'type' => 'select.universal_options_multiple',
//'class' => 'productsearch_form_parameters',
//'method' => 'getConnectedParameters'
//],

    public function getFormComponents(){
        $iconRole = $this->getElementProperty('iconRole');

        if ($iconRole == 2) { // 'role_date'
            return $this->structure_total_top + 
                   $this->structure_role_date + 
                   $this->structure_total_bottom;
        }
        elseif ($iconRole == 4) { // 'role_availability'
            return $this->structure_total_top +
                   $this->structure_role_availability +
                   $this->structure_total_bottom;
        }
        elseif ($iconRole == 5) { // 'role_availability'
            return $this->structure_total_top +
                   $this->structure_role_by_parameter +
                   $this->structure_total_bottom;
        }
        else {
            return $this->structure_total_top +
                   $this->structure_total_bottom;
        }
    }
}