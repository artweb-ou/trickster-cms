<?php

class CatalogueFilterStructure extends ElementForm
{
    protected $formAction = '$currentElement->getFiltrationUrl()';
    protected $formClass = 'catalogue_masseditor';
    protected $structure = [
        'category' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'catalogue_masseditor_categoryselect',
            'options' => '',
        ],
        'brand' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'catalogue_masseditor_brandselect',
            'options' => '',
        ],
        'discount' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'catalogue_masseditor_discountselect',
            'options' => '',
        ],
    ];

}