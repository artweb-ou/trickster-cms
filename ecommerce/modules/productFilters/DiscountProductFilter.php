<?php

class DiscountProductFilter extends productFilter
{
    protected $type = 'discount';

    public function getOptionsInfo()
    {
        if ($this->options === null) {
            $this->options = [];
            if ($discounts = $this->productsListElement->getProductsListDiscounts()) {
                $argumentMap = $this->getArguments();

                foreach ($discounts as &$discount) {
                    if (!$discount->hidden) {
                        $this->options[] = [
                            'title' => $discount->title,
                            'selected' => isset($argumentMap[$discount->id]) || $discount->requested,
                            'id' => $discount->id,
                        ];
                    }
                }
            }
        }
        return $this->options;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterDiscountIds());
    }
}