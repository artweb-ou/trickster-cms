<?php

class FormInputFormStructure extends ElementForm
{
    protected $formClass = 'input_element_form';
    protected $containerClass = 'input_element_container';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'placeholder' => [
            'type' => 'input.text',
            'trClass' => 'row_if_autocomplete',
        ],
        'required' => [
            'type' => 'input.checkbox',
            'trClass' => 'row_if_autocomplete',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
            'class' => 'hide_public',
            'trClass' => 'row_if_autocomplete',
        ],
        'validator' => [
            'type' => 'select.index',
            'options' => [
                'email' => 'validator_email',
                'validDate' => 'validator_datetime',
            ],
            'translationGroup' => 'formfield',
            'defaultRequired' => true,
            'trClass' => 'row_if_autocomplete',
        ],
        'autocomplete' => [
            'type' => 'select.index',
            'method' => 'getAutocompleteSelectOptions',
            'translationGroup' => 'formfield',
            'defaultRequired' => true,
            'class' => 'autocomplete_options'
        ],
    ];
}