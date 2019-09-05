<?php

class InstagramImageFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'instagramId' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.text',
        ],
        'pageSocialId' => [
            'type' => 'input.text',
        ],
    ];
    protected $additionalContent = 'shared.contentlist.tpl';
}