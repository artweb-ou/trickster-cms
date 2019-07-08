<?php

class shoppingBasket implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $formData = [];
    protected $clearOperationPlanned = false;
    /**
     * @var ShoppingBasketCountry[]
     */
    protected $countriesList = [];
    /**
     * @var shoppingBasketDeliveryType[]
     */
    protected $deliveryTypesList = [];
    /**
     * @var shoppingBasketProduct[]
     */
    protected $productsIndex = [];
    /**
     * @var shoppingBasketProduct[]
     */
    protected $productsList = [];
    /**
     * @var shoppingBasketService[]
     */
    protected $servicesList = [];
    /**
     * @var ShoppingBasketDiscount[]
     */
    protected $discountsList = [];
    protected $showInBasketDiscountsList = [];
    protected $selectedDeliveryTypeId = false;
    protected $selectedCountryId = false;
    protected $selectedCityId = false;
    protected $promoCodeDiscountId;
    protected $productsPrice = 0;
    protected $totalPrice = 0;
    protected $deliveryPrice = 0;
    protected $productsAmount = 0;
    protected $vatLessTotalPrice = 0;
    protected $vatAmount = 0;
    protected $selectedServicesPrice = 0;
    protected $message = '';
    protected $vatRate = 0;

    /**
     * @deprecated - architecture should be changed to avoid heavy initializing and to use calculations on demand
     */
    public function initialize()
    {
        $this->loadStorage();
        $this->recalculate();
    }

    public function __destruct()
    {
        if ($this->clearOperationPlanned) {
            $this->clearShoppingBasket();
        }
    }

    protected function saveStorage()
    {
        $user = $this->getService('user');
        $basketData = [];
        $basketData['products'] = [];
        foreach ($this->productsList as &$product) {
            $storageData = $product->getStorageData();
            $basketData['products'][] = $storageData;
        }

        $basketData['formData'] = $this->formData;

        $user->setStorageAttribute('shoppingBasket', $basketData);
    }

    protected function loadStorage()
    {
        $user = $this->getService('user');

        if ($basketData = $user->getStorageAttribute('shoppingBasket')) {
            foreach ($basketData['products'] as &$storageData) {
                $product = new shoppingBasketProduct($storageData);
                if ($product instanceof DependencyInjectionContextInterface) {
                    $this->instantiateContext($product);
                }
                $this->productsList[] = $product;
                $this->productsIndex[$product->basketProductId] = $product;
            }
            $this->formData = $basketData['formData'];
        }
    }

    public function setMessage($text)
    {
        $this->message = $text;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function addProduct($data)
    {
        $parametersString = '';
        $parametersString .= $data['productId'];
        $parametersString .= $data['code'];
        if ((array)$data['variation'] === $data['variation']) {
            $parametersString .= implode(', ', $data['variation']);
        } else {
            $parametersString .= $data['variation'];
        }
        $basketProductId = md5($parametersString);

        if (!isset($this->productsIndex[$basketProductId])) {
            $productData = [];
            $productData['productId'] = $data['productId'];
            $productData['code'] = $data['code'];
            $productData['title'] = $data['title'];
            $productData['title_dl'] = $data['title_dl'];
            $productData['price'] = $data['price'];
            $productData['emptyPrice'] = $data['emptyPrice'];
            $productData['unit'] = $data['unit'];
            $productData['description'] = $data['description'];
            $productData['variation'] = $data['variation'];
            $productData['variation_dl'] = $data['variation_dl'];
            $productData['amount'] = $data['amount'];
            $productData['minimumOrder'] = $data['minimumOrder'];
            $productData['image'] = $data['image'];
            $productData['URL'] = $data['URL'];
            $productData['vatIncluded'] = $data['vatIncluded'];
            $productData['deliveryExtraPrices'] = $data['deliveryExtraPrices'];
            $productData['deliveryPriceType'] = $data['deliveryPriceType'];

            $productData['basketProductId'] = $basketProductId;

            $product = new shoppingBasketProduct($productData);
            if ($product instanceof DependencyInjectionContextInterface) {
                $this->instantiateContext($product);
            }
            $this->productsIndex[$product->basketProductId] = $product;
            $this->productsList[] = $product;
        } else {
            $this->productsIndex[$basketProductId]->addAmount($data['amount']);
        }
        $this->recalculate();
        $this->saveStorage();
    }

    public function changeAmount($basketProductId, $amount)
    {
        if (isset($this->productsIndex[$basketProductId])) {
            $this->productsIndex[$basketProductId]->setAmount($amount);
        }

        $this->recalculate();
        $this->saveStorage();
    }

    public function removeProduct($basketProductId)
    {
        if (isset($this->productsIndex[$basketProductId])) {
            unset($this->productsIndex[$basketProductId]);
        }
        foreach ($this->productsList as $key => &$product) {
            if ($product->basketProductId == $basketProductId) {
                unset($this->productsList[$key]);
                break;
            }
        }

        $this->saveStorage();
        $this->recalculate();
    }

    public function selectDeliveryType($deliveryId)
    {
        $shoppingBasketDeliveryTypes = $this->getService('shoppingBasketDeliveryTypes');
        $shoppingBasketDeliveryTypes->setSelectedDeliveryTypeId($deliveryId);

        $this->recalculate();
    }

    public function setVatRate($vatRate)
    {
        $user = $this->getService('user');
        $user->setStorageAttribute('vatRate', $vatRate);
    }

    protected function getVatRate()
    {
        $user = $this->getService('user');
        $vatRate = $user->getStorageAttribute('vatRate');

        return $vatRate;
    }

    public function selectDeliveryCity($targetId)
    {
        $shoppingBasketDeliveryTargets = $this->getService('ShoppingBasketDeliveryTargets');
        $shoppingBasketDeliveryTargets->setSelectedDeliveryCityId($targetId);

        $this->recalculate();
    }

    public function selectDeliveryCountry($countryId)
    {
        $shoppingBasketDeliveryTargets = $this->getService('ShoppingBasketDeliveryTargets');
        $shoppingBasketDeliveryTargets->setSelectedDeliveryCountryId($countryId);

        $this->recalculate();
    }

    public function setServiceSelection($serviceId, $selected)
    {
        /**
         * @var shoppingBasketServices $shoppingBasketServices
         */
        $shoppingBasketServices = $this->getService('shoppingBasketServices');
        $shoppingBasketServices->setSelection($serviceId, $selected);

        $this->recalculate();
    }

    /**
     * @return bool|shoppingBasketDeliveryType
     * @deprecated
     */
    public function getDeliveryData()
    {
        return $this->getSelectedDeliveryType();
    }

    public function getSelectedDeliveryType()
    {
        $result = false;
        if ($this->selectedDeliveryTypeId) {
            foreach ($this->deliveryTypesList as &$delivery) {
                if ($delivery->id == $this->selectedDeliveryTypeId) {
                    $result = $delivery;
                    break;
                }
            }
        }
        return $result;
    }

    public function clearShoppingBasketDeferred()
    {
        $this->clearOperationPlanned = true;
    }

    public function clearShoppingBasket()
    {
        $this->productsIndex = [];
        $this->productsList = [];

        $this->recalculate();
        $this->saveStorage();
    }

    public function recalculate()
    {
        if ($this->productsList) {
            /**
             * @var ShoppingBasketDeliveryTargets $shoppingBasketDeliveryTargets
             */
            $shoppingBasketDeliveryTargets = $this->getService('ShoppingBasketDeliveryTargets');
            $this->countriesList = $shoppingBasketDeliveryTargets->getActiveCountriesList();

            /**
             * @var shoppingBasketDeliveryTypes $shoppingBasketDeliveryTypes
             */
            $shoppingBasketDeliveryTypes = $this->getService('shoppingBasketDeliveryTypes');
            $this->deliveryTypesList = $shoppingBasketDeliveryTypes->getActiveDeliveryTypes();
            /**
             * @var shoppingBasketServices $shoppingBasketServices
             */
            $shoppingBasketServices = $this->getService('shoppingBasketServices');
            $servicesPrice = 0;
            if ($this->servicesList = $shoppingBasketServices->getServicesList()) {
                foreach ($this->servicesList as &$service) {
                    if ($service->isSelected()) {
                        $servicesPrice += $service->price;
                    }
                }
            }

            $this->selectedCountryId = $shoppingBasketDeliveryTargets->getSelectedCountryId();
            $this->selectedCityId = $shoppingBasketDeliveryTargets->getSelectedCityId();
            $this->selectedDeliveryTypeId = $shoppingBasketDeliveryTypes->getSelectedDeliveryTypeId();

            if (!$regionId = $this->selectedCityId) {
                if (!$regionId = $this->selectedCountryId) {
                    $regionId = 0;
                }
            }

            $productsAmount = 0;
            $productsPrice = 0;
            $productDeliveryPrices = [];
            $selectedDeliveryType = $this->getSelectedDeliveryType();
            foreach ($this->productsIndex as &$product) {
                $productsAmount += $product->amount;
                $productsPrice += $product->totalPrice;
                $productDeliveryPrices[$product->basketProductId] = $product->getDeliveryPrice($selectedDeliveryType,
                    $regionId);
            }

            $deliveryPrice = 0;
            foreach ($this->deliveryTypesList as &$delivery) {
                if ($delivery->id == $this->selectedDeliveryTypeId) {
                    $delivery->setProductDeliveryPrices($productDeliveryPrices);
                    $deliveryPrice = $delivery->getPrice(false, false);
                }
            }
            $totalPrice = $productsPrice + $deliveryPrice;

            $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
            $shoppingBasketDiscounts->calculateProductsListDiscounts($this->productsList, $this->selectedDeliveryTypeId,
                $deliveryPrice, $productsPrice, $totalPrice);
            $totalPrice = $shoppingBasketDiscounts->getTotalPrice();
            $totalPrice += $servicesPrice;
            $this->discountsList = $shoppingBasketDiscounts->getAppliedDiscountsList();

            $vatRateSetting = $this->getService('ConfigManager')->get('main.vatRate');
            if ($vatRateSetting) {
                $this->vatAmount = $totalPrice - $totalPrice / $vatRateSetting;;
                $this->vatLessTotalPrice = $totalPrice - $this->vatAmount;
            }

            $vatRate = $this->getVatRate();
            if (!empty($vatRate) && $vatRate !== $vatRateSetting) {
                $this->vatAmount = $this->vatLessTotalPrice * $vatRate - $this->vatLessTotalPrice;
                $totalPrice = $this->vatLessTotalPrice * $vatRate;
            }

            $currencySelector = $this->getService('CurrencySelector');
            $this->productsAmount = $productsAmount;
            $this->productsPrice = $currencySelector->convertPrice($productsPrice, false);
            if (is_numeric($deliveryPrice)) {
                $this->deliveryPrice = $currencySelector->convertPrice($deliveryPrice);
            } else {
                $this->deliveryPrice = $deliveryPrice;
            }

            $this->selectedServicesPrice = $currencySelector->convertPrice($servicesPrice, false);
            $this->totalPrice = $currencySelector->convertPrice($totalPrice, false);
            $this->vatLessTotalPrice = $currencySelector->convertPrice($this->vatLessTotalPrice, false);
            $this->vatAmount = $currencySelector->convertPrice($this->vatAmount, false);

            if ($this->message == '' && $productsAmount < 1) {
                $translationsManager = $this->getService('translationsManager');
                $this->message = $translationsManager->getTranslationByName('shoppingbasket.basketstatus_empty');
            }
        } else {
            $this->vatAmount = 0;
            $this->vatLessTotalPrice = 0;
            $this->productsAmount = 0;
            $this->productsPrice = 0;
            $this->deliveryPrice = 0;
            $this->totalPrice = 0;

            $translationsManager = $this->getService('translationsManager');
            $this->message = $translationsManager->getTranslationByName('shoppingbasket.basketstatus_empty');
        }

        $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
        $this->showInBasketDiscountsList = [];
        $discounts = $shoppingBasketDiscounts->getDiscountsList();
        foreach ($discounts as $discount) {
            if ($discount->showInBasket) {
                if ($discount->displayProductsInBasket) {
                    if (!$this->productsList || (!$discount->active && !$discount->isUsed())) {
                        $discount->displayText = true;
                    } else {
                        $discount->displayText = false;
                    }
                    $this->showInBasketDiscountsList[] = $discount;
                } else {
                    $hasSutableProducts = false;
                    foreach ($this->productsList as $product) {
                        if ($discount->isProductDiscountable($product->productId)) {
                            $hasSutableProducts = true;
                        }
                    }
                    if (!$discount->active && !$discount->isUsed() && $hasSutableProducts) {
                        $this->showInBasketDiscountsList[] = $discount;
                    }
                }
            }
        }
        if ($promoCodeDiscount = $shoppingBasketDiscounts->getPromoCodeDiscount()) {
            $this->promoCodeDiscountId = $promoCodeDiscount->id;
        } else {
            $this->promoCodeDiscountId = false;
        }
    }

    public function getPromoCodeDiscountId()
    {
        return $this->promoCodeDiscountId;
    }

    //not used
    public function setBasketFormData($formData)
    {
        $this->formData = $formData;
        $this->saveStorage();
    }

    public function updateBasketFormData($formData)
    {
        foreach ($formData as $key => &$value) {
            $this->formData[$key] = $value;
        }

        $this->saveStorage();
    }

    /**
     * Gets all basket products by their corresponding productelement ID
     * @param $productId
     * @return array
     */
    protected function getProductsByProductElementId($productId)
    {
        $products = [];
        foreach ($this->productsIndex as &$basketProduct) {
            if ($basketProduct->productId == $productId) {
                $products[] = $basketProduct;
            }
        }
        return $products;
    }

    /**
     * A product may have been added to the basket before in multiple variations,
     * this method calculates the overall quantity of a certain product element
     * @param $productId
     * @return int
     */
    public function getProductOverallQuantity($productId)
    {
        $quantity = 0;
        if ($products = $this->getProductsByProductElementId($productId)) {
            foreach ($products as &$product) {
                $quantity += $product->amount;
            }
        }
        return $quantity;
    }

    public function getBasketFormData()
    {
        return $this->formData;
    }

    public function getPaymentMethodId()
    {
        if (!empty($this->formData['paymentMethodId'])) {
            return $this->formData['paymentMethodId'];
        }

        return false;
    }

    public function getPaymentMethodElement()
    {
        $paymentMethodId = $this->getPaymentMethodId();
        if ($paymentMethodId) {
            $structureManager = $this->getService('structureManager');
            $paymentElement = $structureManager->getElementById($paymentMethodId);
            return $paymentElement;
        }
        return false;
    }

    public function getCountriesList()
    {
        return $this->countriesList;
    }

    public function getDeliveryPrice()
    {
        return $this->deliveryPrice;
    }

    public function getDeliveryTypesList()
    {
        return $this->deliveryTypesList;
    }

    public function getDiscountsList()
    {
        return $this->discountsList;
    }

    public function getShowInBasketDiscountsList()
    {
        return $this->showInBasketDiscountsList;
    }

    public function getProductsAmount()
    {
        return $this->productsAmount;
    }

    public function getProductsIndex()
    {
        return $this->productsIndex;
    }

    public function getProductsList()
    {
        return $this->productsList;
    }

    public function getProductsPrice()
    {
        $currencySelector = $this->getService('CurrencySelector');
        return $currencySelector->formatPrice($this->productsPrice);
    }

    public function getSelectedCityId()
    {
        return $this->selectedCityId;
    }

    public function getSelectedCity()
    {
        if ($id = $this->getSelectedCityId()) {
            /**
             * @var ShoppingBasketDeliveryTargets $shoppingBasketDeliveryTargets
             */
            $shoppingBasketDeliveryTargets = $this->getService('ShoppingBasketDeliveryTargets');
            return $shoppingBasketDeliveryTargets->getCity($id);
        }
        return false;
    }

    public function getSelectedCountryId()
    {
        return $this->selectedCountryId;
    }

    public function getSelectedCountry()
    {
        if ($id = $this->getSelectedCountryId()) {
            /**
             * @var ShoppingBasketDeliveryTargets $shoppingBasketDeliveryTargets
             */
            $shoppingBasketDeliveryTargets = $this->getService('ShoppingBasketDeliveryTargets');
            return $shoppingBasketDeliveryTargets->getCountry($id);
        }
        return false;
    }

    public function getSelectedDeliveryTypeId()
    {
        return $this->selectedDeliveryTypeId;
    }

    public function getSelectedDeliveryTypeElement()
    {
        $deliveryId = $this->getSelectedDeliveryTypeId();
        if ($deliveryId) {
            $structureManager = $this->getService('structureManager');
            $deliveryElement = $structureManager->getElementById($deliveryId);
            return $deliveryElement;
        }
        return false;
    }

    public function getTotalPrice()
    {
        $currencySelector = $this->getService('CurrencySelector');
        return $currencySelector->formatPrice($this->totalPrice);
    }

    public function getVatAmount($round = true, $useCurrency = true)
    {
        $price = $this->vatAmount;
        $currencySelector = $this->getService('CurrencySelector');
        if ($useCurrency) {
            $price = $currencySelector->convertPrice($price, false);
        }
        if ($round) {
            $price = $currencySelector->formatPrice($price);
        }
        return $price;
    }

    public function getServicesList()
    {
        return $this->servicesList;
    }

    public function getSelectedServicesPrice()
    {
        return $this->selectedServicesPrice;
    }

    public function getSelectedServicesList()
    {
        $selectedServices = [];
        foreach ($this->servicesList as &$service) {
            if ($service->isSelected()) {
                $selectedServices[] = $service;
            }
        }
        return $selectedServices;
    }

    public function getVatLessTotalPrice($round = true, $useCurrency = true)
    {
        $price = $this->vatLessTotalPrice;
        $currencySelector = $this->getService('CurrencySelector');
        if ($useCurrency) {
            $price = $currencySelector->convertPrice($price, false);
        }
        if ($round) {
            $price = $currencySelector->formatPrice($price);
        }
        return $price;
    }

    public function setPromoCode($promoCode)
    {
        $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
        $shoppingBasketDiscounts->setCurrentPromoCode($promoCode);
        $this->recalculate();
        if (!$promoCode) {
            //promocode value was empty to reset current promocode
            return true;
        } elseif ($shoppingBasketDiscounts->getPromoCodeDiscount()) {
            return true;
        }
        return false;
    }
}

class shoppingBasketProduct implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    public $basketProductId = false;
    public $productId = 0;
    public $amount = 0;
    public $minimumOrder = 0;
    public $title = '';
    public $title_dl = '';
    public $code = '';
    public $description = '';
    public $variation = '';
    public $variation_dl = '';
    public $image = '';
    public $deliveryExtraPrices = [];
    public $deliveryPriceType;
    public $relatedCategoriesIds;
    public $relatedBrandsIds;
    public $disabledDeliveryTypeIds;
    public $vatIncluded = false;
    public $discount = 0;
    public $vatAmount = 0;
    public $price = 0;
    public $emptyPrice = false;
    public $unit = '';
    public $totalPrice = 0;
    protected $storageData = [];

    public function __construct($productData)
    {
        $this->storageData = $productData;

        $this->basketProductId = $productData['basketProductId'];
        $this->productId = $productData['productId'];
        $this->amount = $productData['amount'];
        $this->minimumOrder = $productData['minimumOrder'];
        $this->title = $productData['title'];
        $this->title_dl = $productData['title_dl'];
        $this->code = $productData['code'];
        $this->description = $productData['description'];
        $this->variation = $productData['variation'];
        $this->variation_dl = $productData['variation_dl'];
        $this->image = $productData['image'];
        $this->URL = $productData['URL'];
        $this->unit = $productData['unit'];
        $this->vatIncluded = $productData['vatIncluded'];
        $this->deliveryExtraPrices = $productData['deliveryExtraPrices'];
        $this->deliveryPriceType = $productData['deliveryPriceType'];
        $linksManager = $this->getService('linksManager');
        $this->relatedCategoriesIds = $linksManager->getConnectedIdList($this->productId, "catalogue", "child");
        $this->relatedBrandsIds = $linksManager->getConnectedIdList($this->productId, "productbrand", "child");
        $this->emptyPrice = $productData['emptyPrice'];
        $this->recalculate();
    }

    public function getDisabledDeliveryTypesIds()
    {
        $result = [];
        $collection = persistableCollection::getInstance('delivery_type_inactive');
        $relevantIds = (array)($this->relatedCategoriesIds);
        $relevantIds[] = $this->productId;

        $conditions = [
            [
                'targetId',
                'IN',
                $relevantIds,
            ],
        ];
        if ($records = $collection->conditionalLoad('deliveryTypeId', $conditions)) {
            foreach ($records as &$record) {
                $result[] = $record['deliveryTypeId'];
            }
        }
        return $result;
    }

    /**
     * @param shoppingBasketDeliveryType $selectedDeliveryType
     * @return int
     */
    public function getDeliveryPrice($selectedDeliveryType, $regionId)
    {
        if ($selectedDeliveryType) {
            $basePrice = $selectedDeliveryType->getBasePrice();
            if (isset($this->deliveryExtraPrices[$selectedDeliveryType->id][$regionId])) {
                $extraPrice = $this->deliveryExtraPrices[$selectedDeliveryType->id][$regionId];
            } elseif (isset($this->deliveryExtraPrices[$selectedDeliveryType->id][0])) {
                $extraPrice = $this->deliveryExtraPrices[$selectedDeliveryType->id][0];
            }
        } else {
            $basePrice = 0;
        }
        $deliveryPrice = 0;
        if (isset($extraPrice)) {
            if ($this->deliveryPriceType == '1') {
                $deliveryPrice = $basePrice + $extraPrice * $this->amount;
            } else {
                $deliveryPrice = $basePrice + $extraPrice;
            }
        }
        return $deliveryPrice;
    }

    public function addAmount($amount)
    {
        if ($amount >= 0) {
            $this->storageData['amount'] = $this->storageData['amount'] + $amount;
        }
        $this->recalculate();
    }

    public function setAmount($amount)
    {
        if ($amount >= 0) {
            $this->storageData['amount'] = $amount;
        }
        $this->recalculate();
    }

    public function getStorageData()
    {
        return $this->storageData;
    }

    public function recalculate()
    {
        $this->amount = (int)$this->storageData['amount'];
        $this->price = $this->storageData['price'];
        $mainConfig = $this->getService('ConfigManager')->getConfig('main');
        if ($mainConfig->get('pricesContainVat') === false && $mainConfig->has('vatRate') && !$this->vatIncluded) {
            $this->price *= $mainConfig->get('vatRate');
        }
        $this->totalPrice = $this->price * $this->amount;
    }

    /**
     * @return string
     * @deprecated - use getPrice instead
     */
    public function getPriceForDisplaying()
    {
        $currencySelector = $this->getService('CurrencySelector');
        return $currencySelector->convertPrice($this->price);
    }

    public function getPrice($formatted = true, $useCurrency = true)
    {
        $price = $this->price;
        if ($useCurrency) {
            $currencySelector = $this->getService('CurrencySelector');
            $price = $currencySelector->convertPrice($price, $formatted);
        } elseif ($formatted) {
            $currencySelector = $this->getService('CurrencySelector');
            $price = $currencySelector->formatPrice($price);
        }
        return $price;
    }

    /**
     * @return string
     */
    public function getTotalPrice(): string
    {
        $currencySelector = $this->getService('CurrencySelector');
        return $currencySelector->formatPrice($this->totalPrice);
    }
}


class shoppingBasketDeliveryTypes implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    /**
     * @var shoppingBasketDeliveryTypes
     */
    protected static $instance;
    protected $deliveryTypesData;
    protected $selectedDeliveryTypeId;
    protected $activeDeliveryTypes;
    /**
     * @var shoppingBasketDeliveryType[]
     */
    protected $deliveryTypesList = [];
    /**
     * @var shoppingBasketDeliveryType[]
     */
    protected $deliveryTypesIndex = [];

    /**
     * @return shoppingBasketDeliveryTypes
     * @deprecated
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::$instance = $this;
        $this->loadStorage();
    }

    public function saveStorage()
    {
        $languagesManager = $this->getService('languagesManager');;
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        $data = [];
        $data['languageId'] = $currentLanguageId;

        $this->deliveryTypesData = [];
        foreach ($this->deliveryTypesList as &$type) {
            $this->deliveryTypesData[] = $type->getStorageData();
        }
        $data['deliveryTypesData'] = $this->deliveryTypesData;
        $data['selectedDeliveryTypeId'] = $this->selectedDeliveryTypeId;
        $user = $this->getService('user');
        $data['userId'] = $user->id;

        $user->setStorageAttribute('deliveryTypesData', $data);
    }

    protected function loadStorage()
    {
        $user = $this->getService('user');

        $languagesManager = $this->getService('languagesManager');;
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        $data = $user->getStorageAttribute('deliveryTypesData');
        if (!$data || $data['languageId'] != $currentLanguageId || $data['userId'] != $user->id) {
            $this->deliveryTypesData = $this->loadDeliveryTypesData();

            $data = [];
            $data['deliveryTypesData'] = $this->deliveryTypesData;
            $data['selectedDeliveryTypeId'] = false;
        }
        $this->deliveryTypesData = $data['deliveryTypesData'];
        $this->selectedDeliveryTypeId = $data['selectedDeliveryTypeId'];

        foreach ($this->deliveryTypesData as &$storageData) {
            $deliveryType = new shoppingBasketDeliveryType($storageData);
            if ($deliveryType instanceof DependencyInjectionContextInterface) {
                $this->instantiateContext($deliveryType);
            }
            $this->deliveryTypesList[] = $deliveryType;
            $this->deliveryTypesIndex[$deliveryType->id] = $deliveryType;
        }
    }

    protected function loadDeliveryTypesData()
    {
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        $data = [];
        if ($deliveryTypesElementId = $structureManager->getElementIdByMarker('deliveryTypes')) {
            $connectedIds = $linksManager->getConnectedIdList($deliveryTypesElementId, 'structure', 'parent');
            /**
             * @var deliveryTypeElement[] $deliveryTypeElements
             */
            $deliveryTypeElements = $structureManager->getElementsByIdList($connectedIds, false, true);
            foreach ($deliveryTypeElements as &$deliveryTypeElement) {
                $elementData = [];
                $elementData['id'] = $deliveryTypeElement->id;
                $elementData['code'] = $deliveryTypeElement->code;
                $elementData['title'] = $deliveryTypeElement->title;
                $elementData['calculationLogic'] = $deliveryTypeElement->calculationLogic;

                $elementData['deliveryTargetsInfo'] = [];
                if ($pricesIndex = $deliveryTypeElement->getPricesIndex()) {
                    foreach ($pricesIndex as &$record) {
                        $elementData['deliveryTargetsInfo'][] = [
                            'targetId' => $record->targetId,
                            'price' => $record->price,
                        ];
                    }
                }

                $elementData['deliveryFormFields'] = [];
                if ($fieldsList = $deliveryTypeElement->getFieldsList()) {
                    foreach ($fieldsList as &$record) {
                        if ($fieldElement = $structureManager->getElementById($record->fieldId,
                            $deliveryTypeElement->id)) {
                            $fieldInfo = [
                                'id' => $fieldElement->id,
                                'title' => $fieldElement->title,
                                'fieldName' => $fieldElement->fieldName,
                                'fieldType' => $fieldElement->fieldType,
                                'dataChunk' => $fieldElement->dataChunk,
                                'required' => (int)$record->required,
                                'validator' => $fieldElement->validator,
                                'autocomplete' => $fieldElement->autocomplete,
                                'error' => false,
                                'value' => $fieldElement->getAutoCompleteValue(),
                            ];
                            if ($fieldElement->fieldType == 'select') {
                                $fieldInfo['options'] = [];
                                $options = $fieldElement->getOptionsList();
                                foreach ($options as &$option) {
                                    $fieldInfo['options'][] = [
                                        'value' => $option->title,
                                        'text' => $option->title,
                                    ];
                                }
                            } elseif ($fieldElement->fieldType == 'input') {
                                $fieldInfo['helpLinkUrl'] = $fieldElement->helpLinkUrl;
                                $fieldInfo['helpLinkText'] = $fieldElement->helpLinkText;
                            }
                            $elementData['deliveryFormFields'][] = $fieldInfo;
                        }
                    }
                }
                $data[] = $elementData;
            }
        }
        return $data;
    }

    protected function recalculate()
    {
        if ($activeTypes = $this->getActiveDeliveryTypes()) {
            //check if previously selected type still active
            if ($this->selectedDeliveryTypeId) {
                $active = false;
                foreach ($activeTypes as &$type) {
                    if ($type->id == $this->selectedDeliveryTypeId) {
                        $active = true;
                        break;
                    }
                }
                if (!$active) {
                    $this->selectedDeliveryTypeId = false;
                }
            }

            //by default some delivery type should be selected
            if (!$this->selectedDeliveryTypeId) {
                $firstType = reset($activeTypes);
                $this->selectedDeliveryTypeId = $firstType->id;
                $this->saveStorage();
            }
        }
    }

    public function setSelectedDeliveryTypeId($id)
    {
        $this->selectedDeliveryTypeId = $id;
        $this->saveStorage();
    }

    public function getSelectedDeliveryTypeId()
    {
        $this->recalculate();
        return $this->selectedDeliveryTypeId;
    }

    public function getSelectedDeliveryType()
    {
        $this->recalculate();
        return $this->deliveryTypesIndex[$this->selectedDeliveryTypeId];
    }

    public function getDisabledDeliveryTypesIds()
    {
        $result = [];
        /**
         * @var shoppingBasket $shoppingBasket
         */
        $shoppingBasket = $this->getService('shoppingBasket');
        foreach ($shoppingBasket->getProductsList() as &$product) {
            if ($productDisabledIds = $product->getDisabledDeliveryTypesIds()) {
                $result = array_merge($productDisabledIds, $result);
            }
        }
        return array_unique($result);
    }

    public function getActiveDeliveryTypes()
    {
        $this->activeDeliveryTypes = [];
        $excludedDeliveriesIds = $this->getDisabledDeliveryTypesIds();
        $shoppingBasketDeliveryTargets = $this->getService('ShoppingBasketDeliveryTargets');

        foreach ($this->deliveryTypesList as &$deliveryType) {
            if (!$excludedDeliveriesIds || !in_array($deliveryType->id, $excludedDeliveriesIds)) {
                if (in_array($shoppingBasketDeliveryTargets->selectedCityId,
                        $deliveryType->deliveryTargetsIdList) || in_array($shoppingBasketDeliveryTargets->getSelectedCountryId(),
                        $deliveryType->deliveryTargetsIdList)
                ) {
                    $this->activeDeliveryTypes[] = $deliveryType;
                }
            }
        }
        return $this->activeDeliveryTypes;
    }

    public function getDeliveryTypesList()
    {
        return $this->deliveryTypesList;
    }

    public function resetDeliveryType()
    {
        $this->selectedDeliveryTypeId = null;
        $this->recalculate();
    }
}

class shoppingBasketDeliveryType implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $storageData;
    public $id;
    public $title;
    public $code;
    public $deliveryTargetsInfo;
    public $deliveryFormFields;
    public $calculationLogic = 'add';
    protected $productDeliveryPrices = [];
    public $deliveryTargetsIdList = [];

    public function __construct($deliveryData)
    {
        $this->storageData = $deliveryData;

        $this->id = $deliveryData['id'];
        $this->title = $deliveryData['title'];
        $this->code = $deliveryData['code'];
        if ($deliveryData['calculationLogic']) {
            $this->calculationLogic = $deliveryData['calculationLogic'];
        }
        $this->deliveryTargetsInfo = $deliveryData['deliveryTargetsInfo'];
        $this->deliveryFormFields = $deliveryData['deliveryFormFields'];
        foreach ($this->deliveryTargetsInfo as &$targetInfo) {
            $this->deliveryTargetsIdList[] = $targetInfo['targetId'];
        }
    }

    public function getBasePrice()
    {
        $shoppingBasketDeliveryTargets = $this->getService('ShoppingBasketDeliveryTargets');
        $selectedTargetId = $shoppingBasketDeliveryTargets->getSelectedDeliveryTargetId();

        $basePrice = 0;
        foreach ($this->deliveryTargetsInfo as &$info) {
            if ($info['targetId'] == $selectedTargetId) {
                $basePrice = $info['price'];
                break;
            }
        }

        return $basePrice;
    }

    public function setProductDeliveryPrices($productDeliveryPrices)
    {
        $this->productDeliveryPrices = $productDeliveryPrices;
    }

    public function getPrice($round = true, $useCurrency = true)
    {
        $price = '';

        //if base price for delivery is not number, then delivery shouldn't have any numeric price at all.
        if ($this->getBasePrice() !== '') {
            $productsDeliveryPrice = null;

            foreach ($this->productDeliveryPrices as $productId => $productDeliveryPrice) {
                if ($this->calculationLogic == 'add') {
                    $productsDeliveryPrice += $productDeliveryPrice;
                } elseif ($this->calculationLogic == 'useBiggest') {
                    if ($productsDeliveryPrice === null || $productDeliveryPrice > $productsDeliveryPrice) {
                        $productsDeliveryPrice = $productDeliveryPrice;
                    }
                } elseif ($this->calculationLogic == 'useSmallest') {
                    if ($productsDeliveryPrice === null || $productDeliveryPrice < $productsDeliveryPrice) {
                        $productsDeliveryPrice = $productDeliveryPrice;
                    }
                }
            }
            $price = $productsDeliveryPrice + $this->getBasePrice();
        }

        //empty price means "no price defined"
        if (is_numeric($price)) {
            $currencySelector = $this->getService('CurrencySelector');
            if ($useCurrency) {
                $price = $currencySelector->convertPrice($price, false);
            }
            if ($round) {
                $price = $currencySelector->formatPrice($price);
            }
        }

        return $price;
    }

    /**
     * @param $priceQuantifier
     * @deprecated
     */
    public function setPriceQuantifier($priceQuantifier)
    {
    }

    public function setFieldError($fieldName, $error)
    {
        foreach ($this->deliveryFormFields as &$fieldInfo) {
            if ($fieldInfo['fieldName'] == $fieldName) {
                $fieldInfo['error'] = $error;
                $shoppingBasketDeliveryTypes = $this->getService('shoppingBasketDeliveryTypes');
                $shoppingBasketDeliveryTypes->saveStorage();
                break;
            }
        }
    }

    public function setFieldValue($fieldName, $value)
    {
        foreach ($this->deliveryFormFields as &$fieldInfo) {
            if ($fieldInfo['fieldName'] == $fieldName) {
                $fieldInfo['value'] = $value;
                $shoppingBasketDeliveryTypes = $this->getService('shoppingBasketDeliveryTypes');
                $shoppingBasketDeliveryTypes->saveStorage();
                break;
            }
        }
    }

    public function getStorageData()
    {
        $this->storageData['deliveryTargetsInfo'] = $this->deliveryTargetsInfo;
        $this->storageData['deliveryFormFields'] = $this->deliveryFormFields;
        return $this->storageData;
    }
}

class shoppingBasketServices implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    /**
     * @var shoppingBasketServices
     */
    protected static $instance;
    protected $servicesData;
    protected $selectedServices = [];
    /**
     * @var shoppingBasketService[]
     */
    protected $servicesList = [];
    protected $servicesIndex = [];

    /**
     * @return shoppingBasketServices
     * @deprecated
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::$instance = $this;
        $this->loadStorage();
        $this->recalculate();
    }

    public function saveStorage()
    {
        $languagesManager = $this->getService('languagesManager');;
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        $data['languageId'] = $currentLanguageId;
        $data = [];

        $this->servicesData = [];
        foreach ($this->servicesList as &$service) {
            $this->servicesData[] = $service->getStorageData();
        }
        $data['servicesData'] = $this->servicesData;
        $data['selectedServices'] = $this->selectedServices;
        $user = $this->getService('user');
        $data['userId'] = $user->id;

        $user->setStorageAttribute('servicesData', $data);
    }

    protected function loadStorage()
    {
        $user = $this->getService('user');

        $languagesManager = $this->getService('languagesManager');;
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        $data = $user->getStorageAttribute('servicesData');
        if (!$data || $data['languageId'] != $currentLanguageId) {
            $this->servicesData = $this->loadServicesData();

            $data = [];
            $data['servicesData'] = $this->servicesData;
            $data['selectedServices'] = [];
        }
        $this->servicesData = $data['servicesData'];
        $this->selectedServices = $data['selectedServices'];

        foreach ($this->servicesData as &$storageData) {
            $service = new ShoppingBasketService($storageData);
            if ($service instanceof DependencyInjectionContextInterface) {
                $this->instantiateContext($service);
            }
            $this->servicesList[] = $service;
            $this->servicesIndex[$service->id] = $service;
        }
    }

    protected function loadServicesData()
    {
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        $data = [];
        if ($servicesElementId = $structureManager->getElementIdByMarker('shoppingBasketServices')) {
            $connectedIds = $linksManager->getConnectedIdList($servicesElementId, 'structure', 'parent');
            $serviceElements = $structureManager->getElementsByIdList($connectedIds, false, true);
            foreach ($serviceElements as &$serviceElement) {
                $elementData = [];
                $elementData['id'] = $serviceElement->id;
                $elementData['price'] = $serviceElement->price;
                $elementData['title'] = $serviceElement->title;

                $data[] = $elementData;
            }
        }
        return $data;
    }

    public function setSelection($id, $selected)
    {
        if ($selected == false) {
            if (($key = array_search($id, $this->selectedServices)) !== false) {
                unset($this->selectedServices[$key]);
            }
        } else {
            if (!in_array($id, $this->selectedServices)) {
                $this->selectedServices[] = $id;
            }
        }
        $this->recalculate();
        $this->saveStorage();
    }

    public function recalculate()
    {
        foreach ($this->servicesList as &$service) {
            if (in_array($service->id, $this->selectedServices)) {
                $service->setSelected(true);
            } else {
                $service->setSelected(false);
            }
        }
    }

    public function getServicesList()
    {
        return $this->servicesList;
    }
}