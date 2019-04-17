<?php

class SharedFilesStructure extends ElementForm
{
    protected $formClass = 'zxitem_form';
    protected $structure = [
        'connectedFile' => [
            'type' => 'input.file',
        ],
    ];
    protected $additionalContent = 'shared.contentlist';
}