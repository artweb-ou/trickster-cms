<?php

class FormSelectOptionFormStructure extends ElementForm
{
    protected $formClass = 'hidden_fields';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'hidden_fields' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getFieldsToBeHidden',

        ],
    ];

}