<?php

class DeliveryTypeFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'code' => [
            'type' => 'input.text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'calculationLogic' => [
            'type' => 'select.array',
            'options' => ['add', 'useBiggest', 'useSmallest'],
        ],
        'paymentMethodsIds' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'paymentMethods',
            'class' => 'deliverytype_paymentmethod_select',
        ],
    ];

}