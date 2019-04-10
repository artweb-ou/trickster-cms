<?php

class FormFileInputFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'required' => [
            'type' => 'input.checkbox',
        ],
    ];

}