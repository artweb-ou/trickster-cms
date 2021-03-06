<?php

class CollectionFormStructure extends ElementForm
{
    protected $formClass = 'product_form';
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
        'categoryFilterEnable' => [
            'type' => 'input.checkbox',
        ],
        'availabilityFilterEnabled' => [
            'type' => 'input.checkbox',
        ],
        'amountOnPageEnabled' => [
            'type' => 'input.checkbox',
        ],
        'collectionsListIds' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'collectionsListsList',
            'class' => 'select_multiple_categories',
        ],
        'connectedProducts' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'connectedProducts',
            'class' => 'connectedproducts_select',
        ],
    ];

    public function getTranslationGroup()
    {
        return 'brand';
    }
}