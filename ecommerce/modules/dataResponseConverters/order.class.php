<?php

class orderDataResponseConverter extends StructuredDataResponseConverter
{
    protected $defaultPreset = 'api';

    protected function getRelationStructure()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'searchTitle' => 'getSearchTitle',
            'url' => 'getUrl',
            'structureType' => 'structureType',
            'orderNumber' => 'orderNumber',
            'orderStatus' => 'orderStatus',
            'orderStatusText' => 'getOrderStatusText',
            'invoiceNumber' => function ($element) {
                return $element->getInvoiceNumber('invoice');
            },
            'advancePaymentInvoiceNumber' => function ($element) {
                return $element->getInvoiceNumber('advancePaymentInvoice');
            },
            'orderConfirmationNumber' => function ($element) {
                return $element->getInvoiceNumber('orderConfirmation');
            },
            'totalAmount' => 'getTotalAmount',
            'totalPrice' => 'getTotalPrice',
            'productsPrice' => 'getProductsPrice',
            'discountAmount' => 'getDiscountAmount',
            'vatAmount' => 'getVatAmount',
            'dateCreated' => 'dateCreated',
            'currency' => 'currency',
            'payedPrice' => 'getPayedPrice',
            'deliveryPrice' => 'deliveryPrice',
            'deliveryTitle' => 'deliveryTitle',
            'deliveryType' => 'deliveryType',
            'URL' => 'URL',
            'formURL' => function ($element) {
                return $element->URL . 'id:' . $element->id . '/action:showForm/';
            },
            'deleteURL' => function ($element) {
                return $element->URL . 'id:' . $element->id . '/action:delete/';
            },
            'payerName' => 'payerName',
            'payerFirstName' => 'payerFirstName',
            'payerLastName' => 'payerLastName',
            'products' => function ($element) {
                $products = [];
                foreach ($element->getOrderProducts() as $product) {
                    $products[] = $product->getElementData();
                }
                return $products;
            },
            'discounts' => 'getDiscounts',

        ];
    }

    protected function getPresetsStructure()
    {
        return [
            'api' => [
                'id',
                'title',
                'dateCreated',
                'dateModified',
                'url',

                'orderNumber',
                'orderStatus',
                'orderStatusText',
                'invoiceNumber',
                'advancePaymentInvoiceNumber',
                'orderConfirmationNumber',
                'totalAmount',
                'totalPrice',
                'productsPrice',
                'discountAmount',
                'vatAmount',
                'dateCreated',
                'currency',
                'payedPrice',
                'deliveryPrice',
                'deliveryTitle',
                'deliveryType',
                'URL',
                'formURL',
                'deleteURL',
                'payerName',
                'payerFirstName',
                'payerLastName',
                'products',
                'discounts',
            ],
            'search' => [
                'id',
                'searchTitle',
                'url',
                'structureType',
            ],
        ];
    }
}