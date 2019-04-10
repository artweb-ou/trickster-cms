<?php

class ShortcutFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'target' => [
            'type' => 'select.element',
            'property' => 'elementsList',

        ],
    ];

}