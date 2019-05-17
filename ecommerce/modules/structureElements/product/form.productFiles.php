<?php

class ProductFilesStructure extends ElementForm
{
    protected $formClass = 'fileupload_form';
    protected $structure = [
        'connectedFile' => [
            'type' => 'input.file',
            'multiple' => 'true',
            'translationName' => 'files'
        ],
    ];
    protected $additionalContent = 'shared.contentlist';
    protected $additionalControls = false;
}