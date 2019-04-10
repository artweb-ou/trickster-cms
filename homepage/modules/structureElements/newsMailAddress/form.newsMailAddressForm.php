<?php

class NewsMailAddressFormStructure extends ElementForm
{
    protected $structure = [
        'personalName' => [
            'type' => 'input.text',
        ],
        'email' => [
            'type' => 'input.text',
        ],
        'groups' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'groupsList',

        ],
    ];

}