<?php

class discountProductFilter extends productFilter
{
    protected $type = 'discount';

    public function getOptionsInfo()
    {
        $info = [];
        if ($this->options) {
            $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
            $argumentMap = array_flip($this->arguments);

            foreach ($this->options as &$discountId) {
                if ($discount = $shoppingBasketDiscounts->getDiscount($discountId)) {
                    $info[] = [
                        'title' => $discount->title,
                        'selected' => isset($argumentMap[$discount->id]),
                        'id' => $discount->id,
                    ];
                }
            }
        }
        return $info;
    }
    protected function getArguments()
    {
        return true;
    }
//    protected function limitOptions(array $productsIds)
//    {
//        if ($productsIds) {
//            $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
//            $discountsList = $shoppingBasketDiscounts->getApplicableDiscountsList();
//            if ($discountsList) {
//                foreach ($discountsList as &$discount) {
//                    if ($discount->checkProductsListIfApplicable($productsIds)) {
//                        $this->options[] = $discount->id;
//                    }
//                }
//            }
//        }
//    }
//
//    protected function loadRelatedIds()
//    {
//        $this->relatedIds = [];
//        if ($this->arguments) {
//            $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
//            foreach ($this->arguments as $discountId) {
//                if ($discount = $shoppingBasketDiscounts->getDiscount($discountId)) {
//                    $this->relatedIds = array_merge($this->relatedIds, $discount->getApplicableProductsIds());
//                }
//            }
//        }
//    }
}