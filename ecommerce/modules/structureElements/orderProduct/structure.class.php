<?php

/**
 * Class orderProductElement
 *
 * @property string $title
 * @property float $price
 * @property float $oldPrice
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
        $languageManager = $this->getService('languagesManager');
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
        $this->totalPrice = $this->price * $this->amount;
        if ($formatted) {
            return number_format((float)$this->totalPrice, 2, '.', '');
        } else {
            return $this->totalPrice;
        }
    }

    public function getTotalFullPrice($formatted = false)
    {
        if ($this->oldPrice > $this->price) {
            $this->totalPrice = $this->oldPrice * $this->amount;
        } else {
            $this->totalPrice = $this->price * $this->amount;
        }
        if ($formatted) {
            return number_format((float)$this->totalPrice, 2, '.', '');
        } else {
            return $this->totalPrice;
        }
    }
}