<?php

class GalleryLayoutStructure extends ElementForm
{
    protected $structure = [
        'additionalMarkup' => [
            'columns' => [
                'type' => 'input.text',
                'inputType' => 'number',
            ],
            'gap' => [
                'type' => 'input.text',
                'inputType' => 'number',
                'minValue'  => '0',
                'maxValue'  => '5',
                'stepValue' => '1',
                'additionalFormat' => [
                    'labelAfter'  => 'percent_of_row_width',
                ],
            ],
            'captionLayout' => [
                'type' => 'select.index',
                'options' => [
                    'hidden' => 'captionlayout_hidden',
                    'above' => 'captionlayout_above',
                    'below' => 'captionlayout_below',
                    'over' => 'captionlayout_over',
                ],
            ],
            'slideType' => [
                'type' => 'select.index',
                'options' => [
                    'slide' => 'slidetype_slide',
                    'scroll' => 'slidetype_scroll',
                    'carousel' => 'slidetype_carousel',
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