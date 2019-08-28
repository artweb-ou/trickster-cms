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
                    if (!empty($discount->hidden) && !$discount->hidden) {
                        $this->optionsInfo[] = [
                            'title' => $discount->title,
                            'selected' => isset($argumentMap[$discount->id]) || $discount->requested,
                            'id' => $discount->id,
                        ];
                    }
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