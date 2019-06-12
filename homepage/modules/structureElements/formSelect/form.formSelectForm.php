<?php

class FormSelectFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'required' => [
            'type' => 'input.checkbox',
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
        'selectionType' => [
            'type' => 'select.index',
            'options' => [
                'checkbox' => 'selectiontype_checkbox',
                'radiobutton' => 'selectiontype_radiobutton',
                'dropdown' => 'selectiontype_dropdown',
            ],
            'translationGroup' => 'formfield',
        ],
    ];

    protected $additionalContent = 'shared.contentlist_singlepage.tpl';
}