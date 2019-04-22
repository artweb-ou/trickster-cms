<?php

class PriceProductFilter extends productFilter
{
    protected $type = 'price';

    public function __construct(ProductsListElement $element)
    {
        parent::__construct($element);
    }

    public function getOptionsInfo()
    {
        if ($this->options === null) {
            $this->options = [];
            if ($rangeSets = $this->productsListElement->getProductsListPriceRangeSets()) {
                /**
                 * @var CurrencySelector $currencySelector
                 */
                $currencySelector = $this->getService('CurrencySelector');
                $currencyItem = $currencySelector->getSelectedCurrencyItem();
                $argument = $this->getArguments();

                foreach ($rangeSets as $rangeSet) {
                    $min = floor($currencySelector->convertPrice($rangeSet[0]));
                    $max = ceil($currencySelector->convertPrice($rangeSet[1]));
                    $id = $min . '-' . $max;
                    $this->options[] = [
                        'title' => $min . ' - ' . $max . ' ' . $currencyItem->symbol,
                        'selected' => $argument == $id,
                        'id' => $id,
                    ];
                }
            }
        }
        return $this->options;
    }

    protected function getArguments()
    {
        return $this->productsListElement->getFilterPriceString();
    }
}