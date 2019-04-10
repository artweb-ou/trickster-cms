<?php

class CampaignFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'introduction' => [
            'type' => 'input.multi_language_content',
        ],
        'content' => [
            'type' => 'input.multi_language_content',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'shopId' => [
            'type' => 'select.universal_options',
            'property' => 'shopElements',
            'class' => 'translation_form_type',
            'showTitle' => true,
        ],
        'startDate' => [
            'type' => 'input.date',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
    ];
    protected $controls = 'controls';
}