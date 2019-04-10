<?php

class DeliveryTypePricesStructure extends ElementForm
{
    protected $formClass = 'deliverytype_form';
    protected $structure = [
        'prices' => [
            'type' => 'deliveryPrices',
        ],
    ];

}