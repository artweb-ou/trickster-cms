<?php

class GalleryImageFormStructure extends ElementForm
{
    protected $formClass = 'gallery_form_upload';
    protected $structure = [
        'image' => [
            'type' => 'input.image',
        ],
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
    ];

}