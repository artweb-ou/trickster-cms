<?php

class MapFormStructure extends ElementForm
{
    protected $formClass = 'map_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text'
        ],
        'hideTitle' => [
            'type' => 'input.checkbox'
        ],
        'content' => [
            'type' => 'input.html'
        ],
        'image' => [
            'type' => 'input.image'
        ],
        'country' => [
            'type' => 'input.text',
            'textClass' => 'map_country'
        ],
        'region' => [
            'type' => 'input.text',
            'textClass' => 'map_region'
        ],
        'city' => [
            'type' => 'input.text',
            'textClass' => 'map_city'
        ],
        'address' => [
            'type' => 'input.text',
            'textClass' => 'map_address'
        ],
        'zip' => [
            'type' => 'input.text',
            'textClass' => 'map_zip'
        ],
        'description' => [
            'type' => 'input.textarea'
        ],
        'coordinates' => [
            'type' => 'input.coordinates'
        ],
        'zoomLevel' => [
            'type' => 'input.text',
            'inputType' => 'number',
            'minValue'  => '2',
            'maxValue'  => '22',
            'stepValue' => '1',
        ],
        'styles' => [
            'type' => 'input.textarea'
        ],
        'mapTypeControlEnabled' => [
            'type' => 'input.checkbox'
        ],
        'zoomControlEnabled' => [
            'type' => 'input.checkbox'
        ],
        'streetViewControlEnabled' => [
            'type' => 'input.checkbox'
        ],
        'mapCode' => [
            'type' => 'input.textarea'
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',

        ],
    ];

}