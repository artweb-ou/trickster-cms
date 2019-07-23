<?php

class OrderProductFormStructure extends ElementForm
{
    protected $formClass = 'order_product_form';
    protected $structure = [
        'productId' => [

        ],
        'code' => [
            'type' => 'input.text',
        ],
        'title' => [
            'type' => 'input.text',
        ],
        'title_dl' => [
            'type' => 'input.text',
        ],
        'variation' => [
            'type' => 'input.textarea'
        ],
        'variation_dl' => [
            'type' => 'input.textarea'
        ],
        'description' => [
            'type' => 'input.textarea',
        ],
        'price' => [
            'type' => 'input.text',
        ],
        'vatRate' => [
            'type' => 'input.text',
        ],
        'vatLessPrice' => [
            'type' => 'input.text',
        ],
        'amount' => [
            'type' => 'input.text',
        ],
        'unit' => [
            'type' => 'input.text',
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
            'class' => 'product_search',
            'property' => 'product',
            'types' => $this->getSearchTypes(),
        ];
        $this->structure['productId'] = $structure;
        return $this->structure;
    }
}