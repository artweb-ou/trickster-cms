<?php

class PriceProductFilter extends ProductFilter
{
    protected $type = 'price';

    public function getOptionsInfo()
    {
        if ($this->optionsInfo === null) {
            $this->optionsInfo = [];
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
                    $this->optionsInfo[] = [
                        'title' => $min . ' - ' . $max . ' ' . $currencyItem->symbol,
                        'selected' => $argument == $id,
                        'id' => $id,
                    ];
                }
            }
        }
        return $this->optionsInfo;
    }

    protected function getArguments()
    {
        return $this->productsListElement->getFilterPriceString();
    }

    public function getRange()
    {
        return [$this->productsListElement->getProductsListMinPrice(), $this->productsListElement->getProductsListMaxPrice()];
    }

    public function getSelectedRange()
    {
        if ($string = $this->productsListElement->getFilterPriceString()) {
            $parts = explode('-', $string);
            return $parts;
        }
        return $this->getRange();
    }
}