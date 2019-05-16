<?php

class ProductFormStructure extends ElementForm
{
    protected $formClass = 'product_form';
    protected $structure = [
        'inactive' => [
            'type' => 'input.checkbox',
        ],
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'code' => [
            'type' => 'input.text',
        ],
        'price' => [
            'type' => 'input.text',
        ],
        'oldPrice' => [
            'type' => 'input.text',
        ],
        'unit' => [
            'type' => 'input.multi_language_text',
        ],
        'minimumOrder' => [
            'type' => 'input.text',
        ],
        'brandId' => [
            'type' => 'select.element',
            'property' => 'brandsList',
            'defaultRequired' => true,
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'categoriesList',
        ],
        'showincategory' => [
            'type' => 'input.checkbox',
        ],
        'availability' => [
            'type' => 'select.array',
            'options' => ['available', 'quantity_dependent', 'inquirable', 'unavailable', 'available_inquirable'],
            'translationGroup' => 'product',
        ],
        'quantity' => [
            'type' => 'input.text',
        ],
        'discounts' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'discountsList',
        ],
        'products' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'productsInfo',
            'class' => 'connectedproducts_select',
        ],
        'products2' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'productsInfo2',
            'class' => 'connectedproducts_select',
        ],
        'products3' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'productsInfo3',
            'class' => 'connectedproducts_select',
        ],
        'connectedProductCategories' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'connectedProductCategoriesInfo',
            'class' => 'connectedcategories_select',
        ],
        'qtFromConnectedCategories' => [
            'type' => 'input.text',
        ],
    ];

    public function getControls()
    {
        if(empty($this->controls)) {
            $this->controls = [
                'save' => [
                    'class' => 'success_button',
                    'type' => 'submit'
                ],
                'clone' => [
                    'class' => 'classic_button',
                    'action' => 'clone',
                    'icon' => 'clone'
                ],
                'delete' => [
                    'class' => 'warning_button',
                    'action' => 'delete',
                    'icon' => 'delete'
                ],
            ];
        }
        return $this->controls;
    }

}