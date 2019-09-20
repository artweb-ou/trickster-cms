<?php

class DiscountProductFilter extends ProductFilter
{
    protected $type = 'discount';

    public function getOptionsInfo()
    {
        if ($this->optionsInfo === null) {
            $this->optionsInfo = [];
            if ($discounts = $this->productsListElement->getProductsListDiscounts()) {
                $argumentMap = $this->getArguments();

                foreach ($discounts as &$discount) {
                    $this->optionsInfo[] = [
                        'title' => $discount->title,
                        'selected' => isset($argumentMap[$discount->id]),
                        'id' => $discount->id,
                    ];
                }
            }
        }
        return $this->optionsInfo;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterDiscountIds());
    }
}