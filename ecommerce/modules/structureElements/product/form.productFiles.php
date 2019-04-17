<?php

class ProductFilesStructure extends ElementForm
{
    protected $formClass = 'zxitem_form';
    protected $structure = [
        'connectedFile' => [
            'type' => 'input.file',
            'multiple' => 'true'
        ],
    ];
    protected $additionalContent = 'shared.contentlist';
    protected $additionalControls = false;
}