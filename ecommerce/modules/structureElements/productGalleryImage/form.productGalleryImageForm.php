<?php

class ProductGalleryImageFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'link' => [
            'type' => 'input.text',
        ],
        'description' => [
            'type' => 'input.html',
        ],
        'labelText' => [
            'type' => 'input.html',
        ],
    ];
    
}