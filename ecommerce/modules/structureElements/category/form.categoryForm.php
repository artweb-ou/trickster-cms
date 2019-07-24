<?php

class CategoryFormStructure extends ElementForm
{
    protected $structure = [
        'image' => [
            'type' => 'input.image',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
        'unit' => [
            'type' => 'input.multi_language_text',
        ],
        'parameters' => [
            'type' => 'select.parameters_group',
        ],
        'productCataloguesIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getProductCataloguesIds',
            'defaultRequired' => true,
        ],
        'feedbackId' => [
            'type' => 'multi_language_feedback',
            'method' => 'getFeedbackFormList',
            'defaultRequired' => true,
        ],
        'parentCategoriesIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getParentCategoriesList',
            'defaultRequired' => true,
        ],
    ];

}