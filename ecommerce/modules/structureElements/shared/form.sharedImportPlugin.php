<?php

class SharedImportPluginStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'special_fields' => [
            'type' => 'input.special_fields',
        ],
    ];

}