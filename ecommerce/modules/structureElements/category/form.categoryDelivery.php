<?php

class CategoryDeliveryStructure extends ElementForm
{
    protected $structure = [
        'deliveryStatus' => [
            'type' => 'input.multi_language_text',
        ],
        'deliveryprices_additional' => [
            'type' => 'show.heading',
        ],
        'deliveryPriceType' => [
            'type' => 'select.element',
            'options' => [
                ['id' => '0', 'title' => ''],
                ['id' => '1', 'title' => 'type_single'],
                ['id' => '2', 'title' => 'type_general'],
            ],
        ],
    ];
    protected $customComponent = 'component.additionalDeliveryPrices';

}