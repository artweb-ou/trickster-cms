<?php

class CatalogueMasseditStructure extends ElementForm
{
    protected $formAction = '$currentElement->getFiltrationUrl()';
    protected $formClass = 'catalogue_masseditor';
    protected $structure = [
        'newCategories' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'catalogue_masseditor_categoryselect',
            'options' => '',
        ],
        'newBrand' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'catalogue_masseditor_brandselect',
            'options' => '',
        ],
        'newDiscounts' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'catalogue_masseditor_discountselect',
            'options' => '',
        ],
        'productPriceMultiplier' => [
            'type' => 'input.text',
        ],
        'productPriceAddition' => [
            'type' => 'input.text',
        ],
        'targetAll' => [
            'type' => 'input.checkbox',
        ],
        'targets' => [
            'type' => 'controls.mass_edit_buttons',
        ],
    ];
}