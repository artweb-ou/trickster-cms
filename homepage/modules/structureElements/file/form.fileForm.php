<?php

class FileFormStructure extends ElementForm
{
    protected $structure = [
        'file' => [
            'type' => 'input.file',
        ],
        'title' => [
            'type' => 'input.text',
        ],
    ];

}