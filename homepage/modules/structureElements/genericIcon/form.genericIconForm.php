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
        'iconBgColor' => [
            'type' => 'input.text',
            'inputType' => 'color',
            'inputDefaultValue' => '#ffffff',
        ],
        'iconTextColor' => [
            'type' => 'input.text',
            'inputType' => 'color',
            'inputDefaultValue' => '#ffffff',
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
            'method'  => 'productIconLocationOptionsList',
        ],
        'iconRole' => [
            'type' => 'select.index',
            'method' => 'productIconRoleOptionsList',
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
/*
default
date
general_discount
availability
by_parameter
*/
            case '1': //  role_date
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

            case '3': //  role_availability
                $iconRoleDependentFormStructureElement = [
                    'iconProductAvail' => [
                        'type'                      => 'select.serialized',
                        'method'                    => 'productsAvailabilityOptionsList',
                        'valuesTranslationGroup'    => 'product',
                        'class'                     => 'select_simple',
                    ],
                ];
                break;

            case '4': //  role_by_parameter
                $iconRoleDependentFormStructureElement = [
                    'iconProductParameters' => [
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