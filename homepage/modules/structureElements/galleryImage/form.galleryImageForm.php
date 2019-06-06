<?php

class GalleryImageFormStructure extends ElementForm
{
    protected $formClass = 'gallery_form_upload';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'description' => [
            'type' => 'input.html',
        ],
        'alt' => [
            'type' => 'input.text',
        ],
        'externalLink' => [
            'type' => 'input.text',
            'textClass' => 'focused_input',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'mobileImage' => [
            'type' => 'input.image',
        ],
    ];

}