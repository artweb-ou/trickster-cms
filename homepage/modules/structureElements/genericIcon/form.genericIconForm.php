<?php

class GenericIconFormStructure extends ElementForm
{

    protected $formClass = 'genericicon_form applicable_filters_form';
    protected $structure = [];
    protected $formStructureElementTop = [
        'textSettings'  => [
            'type'             => 'show.heading',
            'translationGrupp' => 'shared'
        ],
        'title'         => [
            'type'  => 'input.multi_language_text',
            'class' => 'genericicon_title',
        ],
        'iconBgColor'   => [
            'type'                    => 'input.text',
            'textClass'               => 'jscolor',
            'inputDefaultValueMethod' => [
                'method'   => 'getSettingsVariablles',
                'variable' => 'colors.primary_color',
            ],
            'additionalFormat'        => [
                'labelAfter' => 'select_color',
            ],
        ],
        'iconTextColor' => [
            'type'                    => 'input.text',
            'textClass'               => 'jscolor',
            'inputDefaultValueMethod' => [
                'method'   => 'getSettingsVariablles',
                'variable' => 'colors.primary_color',
            ],
            'additionalFormat'        => [
                'labelAfter' => 'select_color',
            ],
        ],
        'imageSettings' => [
            'type'             => 'show.heading',
            'translationGrupp' => 'shared'
        ],
        'image'         => [
            'type'  => 'input.multi_language_image',
            'class' => 'genericicon_icon',
        ],
        'iconWidth'     => [
            'type'      => 'input.multi_language_text',
            'inputType' => 'number',
            'minValue'  => '0',
            'maxValue'  => '50',
            'stepValue' => '0.5',
        ],
        'iconWidthOnProduct'     => [
            'type'      => 'input.multi_language_text',
            'inputType' => 'number',
            'minValue'  => '0',
            'maxValue'  => '50',
            'stepValue' => '0.5',
        ],
        'iconLocation'  => [
            'type'   => 'select.index',
            'method' => 'productIconLocationOptionsList',
        ],
        'roleSettings'  => [
            'type'             => 'show.heading',
            'translationGrupp' => 'shared'
        ],
        'iconRole'      => [
            'type'   => 'select.index',
            'method' => 'productIconRoleOptionsList',
        ],
    ];
    protected $formStructureElementBottom = [
        'filters'        => [
            'type'             => 'show.heading',
            'translationGrupp' => 'shared'
        ],
        'applicableToAllProducts'      => [
            'type' => 'input.checkbox',
            'class' => 'show_filters'
        ],
        'iconProducts'   => [
            'type'    => 'select.universal_options_multiple',
            'method'  => 'getConnectedProducts',
            'trClass' => 'form_filters_showed',
            'class'   => 'genericicon_form_productselect',
            'dataset' => ["data-select", "product"],
        ],
        'iconCategories' => [
            'type'    => 'select.universal_options_multiple',
            'method'  => 'getConnectedCategoriesInfo',
            'trClass' => 'form_filters_showed',
            'class'   => 'genericicon_form_categoryselect',
            'dataset' => ["data-select", "category"],
        ],
        'iconBrands'     => [
            'type'    => 'select.universal_options_multiple',
            'method'  => 'getConnectedBrands',
            'trClass' => 'form_filters_showed',
            'class'   => 'genericicon_form_brandselect',
            'dataset' => ["data-select", "brand"],
        ],
    ];

    public function getFormComponents()
    {
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
                    'endDate'   => [
                        'type' => 'input.date',
                    ],
                    'days'      => [
                        'type'             => 'input.text',
                        'inputType'        => 'number',
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
                        'type'                   => 'select.serialized',
                        'method'                 => 'productsAvailabilityOptionsList',
                        'valuesTranslationGroup' => 'product',
                        'class'                  => 'select_simple',
                    ],
                ];
                break;

            case '4': //  role_by_parameter
                $iconRoleDependentFormStructureElement = [
                    'iconProductParameters' => [
                        'type'    => 'select.universal_options_multiple',
                        'method'  => 'getConnectedParameters',
                        'class'   => 'genericicon_form_parameterselect',
                        'dataset' => [
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