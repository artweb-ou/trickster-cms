<?php

class PriceProductFilter extends ProductFilter
{
    protected $type = 'price';

    protected $usePresets = true;

    public function __construct(ProductsListElement $element, $initalOptions = [])
    {
        parent::__construct($element, $initalOptions);
        $this->usePresets = $initalOptions['usePresets'];
    }

    public function getOptionsInfo()
    {
        if ($this->optionsInfo === null) {
            $this->optionsInfo = [];
            if ($rangeSets = $this->productsListElement->getProductsListPriceRangeSets()) {
                /**
                 * @var CurrencySelector $currencySelector
                 */
                $currencySelector = $this->getService(CurrencySelector::class);
                $currencyItem = $currencySelector->getSelectedCurrencyItem();
                $argument = $this->getArguments();

                if ($this->usePresets) {
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

                } else {
                    $minOption = null;
                    $maxOption = null;
                    foreach ($rangeSets as $rangeSet) {
                        $min = floor($currencySelector->convertPrice($rangeSet[0], false));
                        if ($minOption === null) {
                            $minOption = $min;
                        }
                        $max = ceil($currencySelector->convertPrice($rangeSet[1], false));
                        if ($maxOption === null || $maxOption < $max) {
                            $maxOption = $max;
                        }
                    }
                    $this->optionsInfo[] = $minOption;
                    $this->optionsInfo[] = $maxOption;
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
        $options = $this->getOptionsInfo();
        return [$options[0], $options[1]];
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