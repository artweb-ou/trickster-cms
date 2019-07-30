<?php

/**
 * Class orderProductElement
 *
 * @property string $title
 * @property float $price
 * @property float $oldPrice
 * @property float $vatRate
 * @property float $vatLessPrice
 * @property int $amount
 * @property int $productId
 */
class orderProductElement extends structureElement
{
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_order_product';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'content';
    protected $totalPrice;
    protected $vatAmount;
    protected $vatLessTotalPrice;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['amount'] = 'text';
        $moduleStructure['code'] = 'text';
        $moduleStructure['description'] = 'text';
        $moduleStructure['productId'] = 'text';
        $moduleStructure['oldPrice'] = 'text';
        $moduleStructure['price'] = 'text';
        $moduleStructure['title'] = 'text';
        $moduleStructure['variation'] = 'text';
        $moduleStructure['title_dl'] = 'text';
        $moduleStructure['variation_dl'] = 'text';
        $moduleStructure['unit'] = 'text';
        $moduleStructure['vatRate'] = 'text';
        $moduleStructure['vatLessPrice'] = 'text';
    }

    public function isEmptyPrice()
    {
        return ($this->price === '') ? true : false;
    }

    /**
     * @return array
     */
    public function getElementData()
    {
        $languageManager = $this->getService('LanguagesManager');
        $defaultLanguage = $languageManager->getDefaultLanguage('adminLanguages');
        $structureManager = $this->getService('structureManager');
        $data = [
            'id' => $this->productId,
            'title' => $this->title,
            'variation' => $this->variation,
            'amount' => $this->amount,
            'oldPrice' => $this->oldPrice,
            'price' => $this->price,
            'category' => '',
            'category_ga' => '',
            'title_ga' => $this->title_dl,
            'variation_ga' => $this->variation_dl,
        ];
        /**
         * @var productElement $productElement
         */
        if ($productElement = $structureManager->getElementById($this->productId)) {
            if ($category = $productElement->getRequestedParentCategory()) {
                $data['category'] = $category->getTitle();
                $data['category_ga'] = $category->getValue('title', $defaultLanguage->id);
            }
        }
        return $data;
    }

    public function getTotalPrice($formatted = false)
    {
        $this->totalPrice = $this->getPrice(false) * $this->amount;
        if ($formatted) {
            $currencySelector = $this->getService('CurrencySelector');
            return $currencySelector->formatPrice($this->totalPrice);
        } else {
            return $this->totalPrice;
        }
    }

    public function getTotalFullPrice($formatted = false)
    {
        if ($this->oldPrice > $this->price) {
            $this->totalPrice = $this->oldPrice * $this->amount;
        } else {
            $this->totalPrice = $this->getPrice(false) * $this->amount;
        }
        if ($formatted) {
            $currencySelector = $this->getService('CurrencySelector');
            return $currencySelector->formatPrice($this->totalPrice);
        } else {
            return $this->totalPrice;
        }
    }

    /**
     * @return float
     */
    public function getPrice($formatted = true)
    {
        if ($formatted) {
            $currencySelector = $this->getService('CurrencySelector');
            return $currencySelector->formatPrice($this->price);
        }
        return $this->price;
    }

    protected function getVatRate()
    {
        if (empty($this->vatRate)) {
            /**
             * @var $mainConfig ConfigManager
             */
            $mainConfig = $this->getService('ConfigManager')->getConfig('main');
            $vatRate = $mainConfig->get('vatRate');
            if (!empty($vatRate)) {
                $this->vatRate = $vatRate;
            } else {
                $this->vatRate = false;
            }
        }
        return $this->vatRate;
    }

    public function getVatAmount()
    {
        if (empty($this->vatAmount)) {
            $this->vatAmount = $this->getTotalPrice() - $this->getVatLessTotalPrice();
        }
        return $this->vatAmount;
    }

    public function getVatLessTotalPrice()
    {
        $totalPrice = $this->getTotalPrice();
        $this->vatLessTotalPrice = $totalPrice / $this->getVatRate();

        return $this->vatLessTotalPrice;
    }

    public function getVatLessPrice()
    {
        if ($this->price !== '') {
            $this->vatLessPrice = $this->getPrice(false) / $this->getVatRate();
        } else {
            $this->vatLessPrice = '';
        }

        return $this->vatLessPrice;
    }

    public function getFullPrice($formatted = false)
    {
        if ($this->oldPrice > $this->price) {
            $fullPrice = $this->oldPrice;
        } else {
            $fullPrice = $this->price;
        }
        if ($formatted) {
            $currencySelector = $this->getService('CurrencySelector');
            return $currencySelector->formatPrice($fullPrice);
        }
        return $fullPrice;
    }
}