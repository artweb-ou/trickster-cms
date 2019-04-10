<?php

class RoomFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'number' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
    ];
    protected $controls = 'controls';
}