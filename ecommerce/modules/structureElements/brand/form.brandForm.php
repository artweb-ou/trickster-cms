<?php

class BrandFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'link' => [
            'type' => 'input.text',
        ],
        'introduction' => [
            'type' => 'input.multi_language_content',
        ],
        'content' => [
            'type' => 'input.multi_language_content',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'priceSortingEnabled' => [
            'type' => 'input.checkbox',
        ],
        'nameSortingEnabled' => [
            'type' => 'input.checkbox',
        ],
        'dateSortingEnabled' => [
            'type' => 'input.checkbox',
        ],
        'parameterFilterEnabled' => [
            'type' => 'input.checkbox',
        ],
        'discountFilterEnabled' => [
            'type' => 'input.checkbox',
        ],
        'availabilityFilterEnabled' => [
            'type' => 'input.checkbox',
        ],
        'amountOnPageEnabled' => [
            'type' => 'input.checkbox',
        ],
        'brandsListsIds' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'brandsListsList',
            'class' => 'select_multiple_categories',
        ],
    ];


    public function getTranslationGroup()
    {
        return 'brand';
    }
}