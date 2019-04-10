<?php

class DeliveryCountryFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'iso3166_1a2' => [
            'type' => 'input.text',
        ],
        'conditionsText' => [
            'type' => 'input.multi_language_content',
        ],
    ];

}