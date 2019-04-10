<?php

class LatestNewsFormStructure extends ElementForm
{
    public $news_amount = [];
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'orderType' => [
            'type' => 'select.index',
            'options' => [
                'desc' => 'order_desc',
                'rand' => 'order_rand',
            ],
        ],
        'newsDisplayType' => [
            'type' => 'select.index',
            'options' => [
                'auto' => 'display_type_auto',
                'manual' => 'display_type_manual',
            ],
        ],
        'itemsOnPage' => [
            'type' => 'input.text',
        ],
        'newsDisplayAmount' => [
            'type' => 'select.index',
            'options' => [
                '-1' => 'display_everything',
            ],
        ],
        'formNewsListsLimitIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getLimitingNewsLists',
            'class' => 'latest_news_newlist_select',
        ],
        'newsManualSearch' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedNews',
            'class' => 'latest_news_connected_select',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

    protected $preset = 'latest_news_modify_block';

    function __construct()
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->structure['newsDisplayAmount']['options'][$i] = $i;
        }
    }
}