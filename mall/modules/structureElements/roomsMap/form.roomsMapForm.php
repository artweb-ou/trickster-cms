<?php

class RoomsMapFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'content' => [
            'type' => 'input.html',
        ],
    ];
    protected $controls = 'controls';
}