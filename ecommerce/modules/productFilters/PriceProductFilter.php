<?php

class PriceProductFilter extends productFilter
{
    const MIN_PRICE_DIFF = 10;
    protected $type = 'price';
    protected $priceRangeOptions;
    protected $relevantIds = [];
    protected $rangeInterval = 10;
    protected $selectedMin = 0;
    protected $selectedMax = 0;
    protected $range;
    protected $allIdsOfDiscountedProducts;

    public function __construct(ProductsListStructureElement $element)
    {
        parent::__construct($element);
        if (count($arguments) === 2) {
            $this->selectedMin = $arguments[0];
            $this->selectedMax = $arguments[1];
        }
    }

    public function getOptionsInfo()
    {
        $info = [];
        $test = $this->getRange();
        $rangeSets = $this->getRangeSets();
        $currencySelector = $this->getService('CurrencySelector');

        foreach ($rangeSets as &$rangeSet) {
            $min = (int)$currencySelector->convertPrice($rangeSet[0]);
            $max = (int)$currencySelector->convertPrice($rangeSet[1]);

            $info[] = [
                'title' => $min . ' - ' . $max . ' ' . $currencySelector->getSelectedCurrencyCode(),
                'selected' => $this->arguments == $rangeSet,
                'id' => $min . '-' . $max,
            ];
        }
        return $info;
    }

    public function setRangeInterval($interval)
    {
        $this->rangeInterval = $interval;
    }

    public function getRange()
    {
        if ($this->range !== null) {
            return $this->range;
        }
        $this->range = false;
        if ($this->relevantIds) {
            $collection = persistableCollection::getInstance('module_product');
            $discountedProductsIds = array_intersect($this->relevantIds, $this->getAllIdsOfDiscountedProducts());
            $notDiscountedProductsIds = $this->relevantIds;
            $minDiscountedPrice = $maxDiscountedPrice = 0;

            if ($discountedProductsIds) {
                $notDiscountedProductsIds = array_diff($notDiscountedProductsIds, $discountedProductsIds);
                $discountsManager = $this->getService('shoppingBasketDiscounts');

                $records = $collection->conditionalLoad(['distinct(id)', 'price'], [
                    [
                        'id',
                        'IN',
                        $discountedProductsIds,
                    ],
                ], [], [], [], true);
                $discountedPrices = [];
                foreach ($records as &$record) {
                    $id = $record['id'];
                    $price = $record['price'];
                    if ($discountAmount = $discountsManager->getProductDiscount($id, $price)
                    ) {
                        $price -= $discountAmount;
                    }
                    $discountedPrices[] = $price;
                }
                if ($discountedPrices) {
                    $minDiscountedPrice = min($discountedPrices);
                    $maxDiscountedPrice = max($discountedPrices);
                }
            }

            if ($notDiscountedProductsIds) {
                $conditions = [
                    [
                        'ID',
                        'IN',
                        $notDiscountedProductsIds,
                    ],
                ];

                $records = $collection->conditionalLoad('MIN(price), MAX(price)', $conditions, [], [], [], true);
                if ($records && $records[0]['MIN(price)'] !== null && $records[0]['MAX(price)'] !== null) {
                    $min = $records[0]['MIN(price)'];
                    $max = $records[0]['MAX(price)'];

                    if ($discountedProductsIds) {
                        $min = min($min, $minDiscountedPrice);
                        $max = max($max, $maxDiscountedPrice);
                    }
                    $this->range = [$min, $max];
                }
            } else {
                $this->range = [$minDiscountedPrice, $maxDiscountedPrice];
            }
            $currencySelector = $this->getService('CurrencySelector');
            $this->range[0] = $currencySelector->convertPrice($this->range[0]);
            $this->range[1] = $currencySelector->convertPrice($this->range[1]);
        }
        return $this->range;
    }

    public function getSelectedRange()
    {
        if ($this->selectedMax - $this->selectedMin > 0) {
            $result = [
                $this->selectedMin,
                $this->selectedMax,
            ];
        } else {
            $result = $this->getRange();
        }
        return $result;
    }

    public function getRangeSets()
    {
        if ($this->relevantIds) {
            $this->priceRangeOptions = [];
            if ($this->rangeInterval) {
                $collection = persistableCollection::getInstance('module_product');
                $conditions = [
                    [
                        'ID',
                        'IN',
                        $this->relevantIds,
                    ],
                ];

                if ($records = $collection->conditionalLoad('distinct(price)', $conditions, ['price' => 'asc'], [], [], true)
                ) {
                    $prices = [];
                    foreach ($records as &$record) {
                        $prices[] = ceil($record['price']);
                    }
                    $priceCount = count($prices);
                    $priceChunks = array_chunk($prices, max(ceil($priceCount / $this->rangeInterval), 2));

                    foreach ($priceChunks as $priceChunk) {
                        $this->priceRangeOptions[] = [
                            $priceChunk[0],
                            array_pop($priceChunk),
                        ];
                    }
                }
            }
        }
        return $this->priceRangeOptions;
    }

    protected function limitOptions(array $productsIds)
    {
        if ($productsIds) {
            $this->relevantIds = $productsIds;
        }
    }

    protected function loadRelatedIds()
    {
        $this->relatedIds = [];
        if (count($this->arguments) !== 2) {
            return;
        }

        $conditions = [];
        $currencySelector = $this->getService('CurrencySelector');

        $minPrice = (int)$this->arguments[0];
        if ($currencySelector->getSelectedCurrencyRate() <> 1) {
            $minPrice /= $currencySelector->getSelectedCurrencyRate();
        }
        $conditions[] = [
            'price',
            '>=',
            $minPrice,
        ];

        $maxPrice = (int)$this->arguments[1];
        if ($currencySelector->getSelectedCurrencyRate() <> 1) {
            $maxPrice /= $currencySelector->getSelectedCurrencyRate();
        }
        $conditions[] = [
            'price',
            '<=',
            $maxPrice,
        ];

        if ($discountedProductsIds = $this->getAllIdsOfDiscountedProducts()) {
            $discountsManager = $this->getService('shoppingBasketDiscounts');

            $records = persistableCollection::getInstance('module_product')->conditionalLoad([
                'id',
                'price',
            ], [
                [
                    'id',
                    'IN',
                    $discountedProductsIds,
                ],
            ]);
            foreach ($records as &$record) {
                $id = $record['id'];
                $price = $record['price'];
                if ($discountAmount = $discountsManager->getProductDiscount($id, $price)
                ) {
                    $price -= $discountAmount;
                }
                if ($price >= $minPrice && $price <= $maxPrice) {
                    $this->relatedIds[] = $id;
                }
            }
            $conditions[] = [
                'id',
                'NOT IN',
                $discountedProductsIds,
            ];
        }

        $records = persistableCollection::getInstance('module_product')->conditionalLoad('id', $conditions);
        if ($records) {
            foreach ($records as &$record) {
                $this->relatedIds[] = $record['id'];
            }
        }
    }

    protected function getAllIdsOfDiscountedProducts()
    {
        if ($this->allIdsOfDiscountedProducts === null) {
            $this->allIdsOfDiscountedProducts = [];
            $discounts = $this->getService('shoppingBasketDiscounts')->getApplicableDiscountsList();
            foreach ($discounts as &$discount) {
                $this->allIdsOfDiscountedProducts = array_merge($this->allIdsOfDiscountedProducts, $discount->getApplicableProductsIds());
            }
        }
        return $this->allIdsOfDiscountedProducts;
    }

    public function isRelevant()
    {
        $range = $this->getRange();
        if ($range[1] - $range[0] > self::MIN_PRICE_DIFF) {
            return true;
        }
        return false;
    }

    protected function getArguments()
    {
        return true;
    }
}