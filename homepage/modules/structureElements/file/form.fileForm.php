<?php

class FileFormStructure extends ElementForm
{
    protected $structure = [
        'file' => [
            'type' => 'input.file',
            'fileNameProperty' => 'fileName',
        ],
        'title' => [
            'type' => 'input.text',
        ],
    ];

}