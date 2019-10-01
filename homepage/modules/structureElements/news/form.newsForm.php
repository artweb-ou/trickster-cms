<?php

class NewsFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'subTitle' => [
            'type' => 'input.textarea',
            'textarea_class' => 'short_textarea',
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