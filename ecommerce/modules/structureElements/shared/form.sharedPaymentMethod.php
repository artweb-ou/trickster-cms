<?php

class SharedPaymentMethodStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'description' => [
            'type' => 'input.multi_language_content',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'deliveryTypesIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDeliveryTypes',
            'class' => 'deliverytype_paymentmethod_select',
        ],
        'sendOrderConfirmation' => [
            'type' => 'input.checkbox',
        ],
        'sendAdvancePaymentInvoice' => [
            'type' => 'input.checkbox',
        ],
        'sendInvoice' => [
            'type' => 'input.checkbox',
        ],
    ];


    public function getTranslationGroup()
    {
        return 'paymentmethod';
    }
}