<?php

class ArticleFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'image' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
            'filename' => 'image',
        ],
        'allowComments' => [
            'type' => 'input.checkbox',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];
    protected $additionalContent = 'shared.contentlist.tpl';
}