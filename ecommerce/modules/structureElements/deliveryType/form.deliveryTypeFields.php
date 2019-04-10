<?php

class DeliveryTypeFieldsStructure extends ElementForm
{
    protected $formClass = 'deliverytype_form';
    protected $structure = [
        'prices' => [
            'type' => 'deliveryFields',
        ],
    ];

}