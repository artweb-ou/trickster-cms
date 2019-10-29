<?php

class LinkListLayoutStructure extends ElementForm
{
    protected $structure = [
        'additionalMarkup' => [
            'cols' => [
                'type' => 'input.text',
                'inputType' => 'number',
                'minValue'  => '2',
                'maxValue'  => '4',
                'stepValue' => '1',
            ],
            'colWidthValue' => [
                'type' => 'input.text',
                'blockClass' => 'col_2',
                'inputType' => 'number',
                'minValue'  => '',
                'stepValue' => '1',
            ],
            'colWidthUnit' => [
                'type' => 'select.index',
                'blockClass' => 'col_2',
                'options' => [
                    0 => 'none',
                    '%' => 'pct',
                    'pt' => 'px',// @pt
                ],
            ],
            'gapValue' => [
                'type' => 'input.text',
                'blockClass' => 'col_2',
                'inputType' => 'number',
                'minValue'  => '',
                'stepValue' => '1',
            ],
            'gapUnit' => [
                'type' => 'select.index',
                'blockClass' => 'col_2',
                'options' => [
                    0 => 'none',
                    '%' => 'pct',
                    'pt' => 'px',// @pt
                ],
            ],
            'titlePosition' => [
                'type' => 'select.index',
                'options' => [
                    'hidden' =>  'hidden',
                    'above'  =>  'above',
                    'below'  =>  'below',
                    'over'   => 'over',
                ],
            ],
        ]
    ];


    public function getFormComponents()
    {
        $structure = [];
        foreach ($this->element->getLayoutTypes() as $type) {
            $structure[$type] = [
                'type' => 'input.layouts_selection',
                'defaultLayout' => $this->element->getDefaultLayout($type),
                'layouts' => $this->element->getLayoutsSelection($type),
            ];
        }
        $structure['additionalMarkupLayout'] = [
            'type' => 'additional.markup.layout',
            'layouts' => $this->structure['additionalMarkup'],
        ];
        return $structure;
    }
}