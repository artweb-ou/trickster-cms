<?php

class FormInputFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'placeholder' => [
            'type' => 'input.text',
        ],
        'required' => [
            'type' => 'input.checkbox',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
            'class' => 'hide_public',
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
    ];

}