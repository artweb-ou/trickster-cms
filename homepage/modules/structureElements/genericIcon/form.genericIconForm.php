<?php

class GenericIconFormStructure extends ElementForm
{

    protected $formClass = 'genericicon_form';
    protected $structure = [];
    protected $formStructureElementTop = [
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
        ],
    ];
    protected $formStructureElementBottom = [
        'products' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedProducts',
            'class' => 'genericicon_form_productselect',
            'dataset' => ["data-select", "product"],
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedCategoriesInfo',
            'class' => 'genericicon_form_categoryselect',
            'dataset' => ["data-select", "category"],
        ],
        'brands' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedBrands',
            'class' => 'genericicon_form_brandselect',
            'dataset' => ["data-select", "brand"],
        ],
    ];

    public function getFormComponents(){
        $iconRole = $this->getElementProperty('iconRole');
        $iconRoleDependentFormStructureElement = [];
        switch ($iconRole) {
            case '2': //  role_date
                $iconRoleDependentFormStructureElement = [
                    'startDate' => [
                        'type' => 'input.date',
                    ],
                    'endDate' => [
                        'type' => 'input.date',
                    ],
                    'days' => [
                        'type' => 'input.text',
                        'inputType' => 'number',
                        'additionalFormat' => [
                            'labelBefore' => 'days_before',
                            'labelAfter'  => 'days_after',
                        ],
                    ],
                ];
                break;

            case '4': //  role_availability
                $iconRoleDependentFormStructureElement = [
                    'iconProductAvail' => [
                        'type'              => 'select.serialized',
                        'method'            => 'getProductsAvailabilityOptions',
                        'translationGroup'  => 'product',
                        'class'             => 'select_simple',
                    ],
                ];
                break;

            case '5': //  role_by_parameter
                $iconRoleDependentFormStructureElement = [
                    'parameters' => [
                        'type'      => 'select.universal_options_multiple',
                        'method'    => 'getConnectedParameters',
                        'class'     => 'genericicon_form_parameterselect',
                        'dataset'   => [
                            "data-select", // dataset name
                            "productSelectionValue" // dataset argument
                        ],
                    ],
                ];
                break;

            default:
                break;
        }

        return $this->formStructureElementTop +
            $iconRoleDependentFormStructureElement +
            $this->formStructureElementBottom;
    }
}