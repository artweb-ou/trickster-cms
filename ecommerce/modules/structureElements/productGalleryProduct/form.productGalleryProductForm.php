<?php

class ProductGalleryProductFormStructure extends ElementForm
{
    protected $formClass = 'productgalleryproduct_form';
    protected $structure = [
        'productIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedProductElements',
            'class' => 'productgalleryproduct_form_productselect',
        ],
        'positionX'   => [
            'type' => 'input.text',
        ],
        'positionY'   => [
            'type' => 'input.text',
        ],
        'title'       => [
            'type' => 'input.text',
        ],
        'image'       => [
            'type' => 'input.image',
        ],
        'description' => [
            'type' => 'input.html',
        ],
        'code'        => [
            'type' => 'input.text',
        ],
        'price'       => [
            'type' => 'input.text',
        ],
    ];

}