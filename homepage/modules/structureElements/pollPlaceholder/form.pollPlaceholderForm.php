<?php

class PollPlaceholderFormStructure extends ElementForm
{
    protected $structure = [
        'pollId' => [
            'type' => 'select.element',
            'property' => 'pollsList',
            'defaultRequired' => true,
        ],

    ];

}