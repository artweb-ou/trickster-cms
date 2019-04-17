<?php

class brandProductFilter extends productFilter
{
    protected $type = 'brand';

    public function getOptionsInfo()
    {
        if ($this->options === null) {
            $this->options = [];
            if ($brands = $this->productsListElement->getProductsListBrands()) {
                $argumentMap = $this->getArguments();

                foreach ($brands as &$brand) {
                    if (!$brand->hidden) {
                        $this->options[] = [
                            'title' => $brand->title,
                            'selected' => isset($argumentMap[$brand->id]) || $brand->requested,
                            'id' => $brand->id,
                            'url' => $brand->URL,
                        ];
                    }
                }
            }
        }
        return $this->options;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterBrandIds());
    }
}