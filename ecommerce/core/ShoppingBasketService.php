<?php

/**
 * Class shoppingBasketService
 */
class ShoppingBasketService implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $storageData;
    public $id;
    public $title;
    public $price; // TODO make price protected, deprecated
    protected $selected = false;


    /**
     * shoppingBasketService constructor.
     * @param array $deliveryData
     */
    public function __construct(array $deliveryData)
    {
        $this->storageData = $deliveryData;

        $this->id = $deliveryData['id'];
        $this->title = $deliveryData['title'];
        $this->price = ($deliveryData['price']);
    }

    /**
     * @return array
     */
    public function getStorageData(): array
    {
        return $this->storageData;
    }

    /**
     * @param bool $value
     */
    public function setSelected(bool $value)
    {
        $this->selected = $value;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @param bool $round
     * @param bool $useCurrency
     * @return string
     */
    public function getPrice(bool $round = true, bool $useCurrency = true): string
    {
        $price = $this->price;
        if ($useCurrency) {
            $currencySelector = $this->getService('CurrencySelector');
            if ($currencySelector) {
                $price = $currencySelector->convertPrice($price);
            }
        }
        if ($round) {
            $price = sprintf('%01.2f', $price);
        }
        return $price;
    }
}