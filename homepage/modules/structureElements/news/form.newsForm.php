<?php

class NewsFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'date' => [
            'type' => 'input.date',
        ],
        'introduction' => [
            'type' => 'input.html',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'image' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
            'filename' => 'originalName',
        ],
        'thumbImage' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
            'filename' => 'thumbImageOriginalName',
        ],
    ];

}