<?php

class BrandProductFilter extends ProductFilter
{
    protected $type = 'brand';

    public function getOptionsInfo()
    {
        if ($this->optionsInfo === null) {
            $this->optionsInfo = [];
            if ($brands = $this->productsListElement->getProductsListBrands()) {
                $argumentMap = $this->getArguments();

                foreach ($brands as &$brand) {
                    if (!$brand->hidden) {
                        $this->optionsInfo[] = [
                            'title' => $brand->title,
                            'selected' => isset($argumentMap[$brand->id]) || $brand->requested,
                            'id' => $brand->id,
                            'url' => $brand->URL,
                        ];
                    }
                }
            }
        }
        return $this->optionsInfo;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterBrandIds());
    }
}