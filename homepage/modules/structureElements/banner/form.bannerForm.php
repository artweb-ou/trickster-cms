<?php

class BannerFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'link' => [
            'type' => 'input.text',
        ],
        'clickTag' => [
            'type' => 'input.text',
        ],
        'width' => [
            'type' => 'input.text',
        ],
        'height' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'bannerCategoryIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedCategories',
            'class' => 'banner_form_categoryselect',
        ],
        'openInNewWindow' => [
            'type' => 'input.checkbox',
        ],
    ];

}