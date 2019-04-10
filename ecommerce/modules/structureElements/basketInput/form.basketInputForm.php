<?php

class BasketInputFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'validator' => [
            'type' => 'select.index',
            'options' => [
                'email' => 'validator_email',
                'validDate' => 'validator_datetime',
            ],
            'translationGroup' => 'formfield',
            'defaultRequired' => true,
        ],
        'autocomplete' => [
            'type' => 'select.index',
            'method' => 'getAutocompleteSelectOptions',
            'translationGroup' => 'formfield',
            'defaultRequired' => true,
        ],
        'helpLinkText' => [
            'type' => 'input.multi_language_text',
        ],
        'helpLinkUrl' => [
            'type' => 'input.multi_language_text',
        ],
    ];

}