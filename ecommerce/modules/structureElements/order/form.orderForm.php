<?php

class OrderFormStructure extends ElementForm
{
    protected $formClass = 'order_form';
    protected $structure = [
        'orderConfirmation' => [
            'type' => 'block.invoice',
        ],
        'advancePaymentInvoice' => [
            'type' => 'block.invoice',
        ],
        'invoice' => [
            'type' => 'block.invoice',
        ],
        'orderNumber' => [
            'type' => 'input.text',
        ],
        'dateCreated' => [
            'type' => 'input.date',
        ],
        'dueDate' => [
            'type' => 'input.date',
        ],
        'orderStatus' => [
            'type' => 'select.index',
            'options' => [
                'undefined' => 'status_undefined',
                'payed' => 'status_payed',
                'failed' => 'status_failed',
                'sent' => 'status_sent',
                'deleted' => 'status_deleted',
                'paid_partial' => 'status_paid_partial',
            ],
            'translationGroup' => 'order',
            'additionalCell' => [
                'additionalFieldName' => 'Notification',
                'template' => 'component.block.status.notification.tpl'
            ],
        ],
        'currency' => [
            'type' => 'input.text',
        ],
        'deliverydata' => [
            'type' => 'show.heading',
        ],
        'deliveryTitle' => [
            'type' => 'input.text',
        ],
        'deliveryPrice' => [
            'type' => 'input.text',
        ],
        'receiverdata' => [
            'type' => 'show.receive_fields',
        ],
        'payerdata' => [
            'type' => 'show.heading',
        ],
        'userId' => [

        ],
        'payerCompany' => [
            'type' => 'input.text',
        ],
        'payerFirstName' => [
            'type' => 'input.text',
        ],
        'payerLastName' => [
            'type' => 'input.text',
        ],
        'payerEmail' => [
            'type' => 'input.text',
        ],
        'payerPhone' => [
            'type' => 'input.text',
        ],
        'payerCity' => [
            'type' => 'input.text',
        ],
        'payerAddress' => [
            'type' => 'input.text',
        ],
        'payerPostIndex' => [
            'type' => 'input.text',
        ],
        'payerCountry' => [
            'type' => 'input.text',
        ],
        'discounts' => [
            'type' => 'show.lists',
            'method' => 'getDiscountsList',
        ],
        'services' => [
            'type' => 'show.lists',
            'method' => 'getServicesList',
        ],
    ];


    protected function getSearchTypes()
    {
        return $this->element->getSearchTypesString('admin');
    }

    public function getFormComponents()
    {
        $structure = [
            'type' => 'ajaxsearch',
            'class' => 'order_form_search',
            'method' => 'getUserElement',
            'types' => $this->getSearchTypes(),
        ];
        $this->structure['userId'] = $structure;
        return $this->structure;
    }
}