<?php

class PaymentFormStructure extends ElementForm
{
    protected $structure = [
        'numbers' => [
            'type' => 'show.payment_order_number',
        ],
        'orderId' => [
            'type' => 'input.text',
        ],
        'userId' => [],
        'account' => [
            'type' => 'input.text',
        ],
        'payer' => [
            'type' => 'input.text',
        ],
        'bank' => [
            'type' => 'input.text',
        ],
        'amount' => [
            'type' => 'input.text',
        ],
        'currency' => [
            'type' => 'input.text',
        ],
        'date' => [
            'type' => 'input.date',
        ],
        'paymentStatus' => [
            'type' => 'select.index',
            'options' => [
                'undefined' => 'payment_undefined',
                'success' => 'payment_success',
                'fail' => 'payment_fail',
                'invalid' => 'payment_invalid',
            ],
            'translationGroup' => 'payment',
        ],

    ];

    protected $preset = 'payment_form';

    protected function getSearchTypes()
    {
        return $this->element->getSearchTypesString('admin');
    }

    public function getFormComponents()
    {
        $structure = [
            'type' => 'ajaxsearch',
            'class' => 'payment_form_search',
            'property' => 'user',
            'types' => $this->getSearchTypes(),
        ];
        $this->structure['userId'] = $structure;
        return $this->structure;
    }
}