<?php

class OpeningHoursInfoFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'exceptional' => [
            'type' => 'input.checkbox',
        ],
    ];
    protected $controls = 'controls';
}