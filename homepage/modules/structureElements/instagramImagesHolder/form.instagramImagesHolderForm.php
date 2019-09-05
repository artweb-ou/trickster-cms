<?php

class InstagramImagesHolderFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'pageSocialId' => [
            'type' => 'select.element',
            'method' => 'getFacebookPages',
        ],
    ];
    protected $additionalContent = 'shared.contentlist.tpl';
}