<?php

class SocialPageFormStructure extends ElementForm
{
    protected $formData;
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'socialId' => [
            'type' => 'input.text',
        ]
    ];
}