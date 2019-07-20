<?php

class NewsListLayoutStructure extends ElementForm
{
    protected $structure = [
        'cols' => [
            'type' => 'input.text',
            'inputType' => 'number',
            'minValue'  => '2',
            'maxValue'  => '4',
            'stepValue' => '1',
        ],
        'generalOwnerName' => [
            'type' => 'input.text',
        ],
        'generalOwnerAvatar' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
            'fileName' => 'generalOwnerAvatarOriginalName',
        ],
        'socMedia_1_Name' => [
            'type' => 'select.index',
            'options' => [
                'fb' => 'fb', //'Facebook',
                'tw' => 'tw', //'Twitter',
                'gl' => 'gl', //'Google+',
                'li' => 'li', //'LinkedIn',
            ],
        ],

        'socMedia_1_Icon' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
            'fileName' => 'socMedia_1_IconOriginalName',
        ],
//        'socMedia_1_Link' => [
//            'type' => 'input.text',
//        ],

        'captionLayout' => [
            'type' => 'select.index',
            'options' => [
                'hidden' => 'captionlayout_hidden',
                'above' => 'captionlayout_above',
                'below' => 'captionlayout_below',
                'over' => 'captionlayout_over',
            ],
        ],
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
        return $structure + $this->structure;
    }
}