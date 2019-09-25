<?php

class shoppingBasketDiscounts implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $currentPromoCode;
    protected $discountsData;
    /**
     * @var ShoppingBasketDiscount[]
     */
    protected $applicableDiscountsList;
    /**
     * @var ShoppingBasketDiscount[]
     */
    protected $discountsList = [];
    /**
     * @var ShoppingBasketDiscount[]
     */
    protected $discountsIndex = [];
    protected $deliveryPrice;
    protected $totalPrice;
    protected $appliedDiscountsList = [];
    protected $discountAmountsIndex = [];

    /**
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return mixed
     */
    public function getDeliveryPrice()
    {
        return $this->deliveryPrice;
    }

    /**
     * @return array
     */
    public function getAppliedDiscountsList()
    {
        return $this->appliedDiscountsList;
    }

    /**
     * @return mixed
     */
    public function getCurrentPromoCode()
    {
        return $this->currentPromoCode;
    }

    /**
     * @param mixed $currentPromoCode
     */
    public function setCurrentPromoCode($currentPromoCode)
    {
        $this->currentPromoCode = $currentPromoCode;
        $this->saveStorage();
    }

    public function getPromoCodeDiscount()
    {
        foreach ($this->discountsList as $discount) {
            if ($discount->hasActivePromoCode()) {
                return $discount;
            }
        }
        return false;
    }

    public function hasPromoDiscounts()
    {
        foreach ($this->discountsList as $discount) {
            if ($discount->acceptsPromoCode()) {
                return $discount;
            }
        }
        return false;
    }

    /**
     * @deprecated - architecture should be changed to avoid heavy initializing and to use calculations on demand
     */
    public function initialize()
    {
        $this->loadStorage();
    }

    public function saveStorage()
    {
        $serverSessionManager = $this->getService('ServerSessionManager');
        if ($this->currentPromoCode) {
            $serverSessionManager->set('currentPromoCode', $this->currentPromoCode);
        } else {
            $serverSessionManager->delete('currentPromoCode');
        }

        $languagesManager = $this->getService('LanguagesManager');
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        $data = [];
        $data['languageId'] = $currentLanguageId;
        $data['discountsData'] = $this->discountsData;

        $cache = $this->getService('cache');
        $cache->set('discountsData' . $currentLanguageId, $data, 600);
    }

    protected function loadStorage()
    {
        $serverSessionManager = $this->getService('ServerSessionManager');
        $this->currentPromoCode = $serverSessionManager->get('currentPromoCode');

        $cache = $this->getService('cache');
        $languagesManager = $this->getService('LanguagesManager');
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        if ($data = $cache->get('discountsData' . $currentLanguageId)) {
            $this->discountsData = $data['discountsData'];
        } else {
            $this->discountsData = $this->loadDiscountsData();
            $this->saveStorage();
        }

        foreach ($this->discountsData as &$storageData) {
            $discount = new ShoppingBasketDiscount($storageData, $this);
            $this->instantiateContext($discount);
            $this->discountsList[] = $discount;
            $this->discountsIndex[$discount->id] = $discount;
        }
    }

    protected function loadDiscountsData()
    {
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');

        $data = [];
        if ($discountsElementId = $structureManager->getElementIdByMarker('discounts')) {
            $discountElements = [];
            $connectedIds = $linksManager->getConnectedIdList($discountsElementId, 'structure', 'parent');
            foreach ($connectedIds as &$id) {
                if ($discountElement = $structureManager->getElementById($id)) {
                    $discountElements[] = $discountElement;
                }
            }

            foreach ($discountElements as $discountElement) {
                $elementData = [];
                $elementData['id'] = $discountElement->id;
                $elementData['code'] = $discountElement->code;
                $elementData['title'] = $discountElement->title;
                $elementData['logic'] = $discountElement->logic;
                $elementData['startDate'] = $discountElement->getTimeStamp($discountElement->startDate);
                $elementData['endDate'] = $discountElement->getTimeStamp($discountElement->endDate);
                $elementData['promoCode'] = $discountElement->promoCode;
                $elementData['conditionPrice'] = $discountElement->conditionPrice;
                $elementData['conditionPriceMax'] = $discountElement->conditionPriceMax;
                $elementData['conditionUserGroupId'] = $discountElement->conditionUserGroupId;
                $elementData['discountedProductsIds'] = $discountElement->getConnectedProductsIds();
                $elementData['discountedCategoriesIds'] = $discountElement->getConnectedCategoriesIds();
                $elementData['discountedBrandIds'] = $discountElement->getConnectedBrandsIds();
                $elementData['targetAllProducts'] = $discountElement->targetAllProducts;
                $elementData['groupBehaviour'] = $discountElement->groupBehaviour;
                $elementData['productDiscount'] = $discountElement->productDiscount;
                $elementData['fixedPrice'] = (float)$discountElement->fixedPrice;
                $elementData['deliveryTypesDiscountsIndex'] = $discountElement->getDeliveryTypesDiscountsIndex();
                $elementData['showInBasket'] = $discountElement->showInBasket;
                $elementData['basketText'] = $discountElement->basketText;
                $elementData['displayText'] = $discountElement->displayText;
                $elementData['displayProductsInBasket'] = $discountElement->displayProductsInBasket;
                $data[] = $elementData;
            }
        }
        return $data;
    }

    public function getDiscountsList()
    {
        return $this->discountsList;
    }

    /**
     * Returns a list of all discounts having discounted products at the moment.
     *
     * @return ShoppingBasketDiscount[]
     */
    public function getApplicableDiscountsList()
    {
        if ($this->applicableDiscountsList === null) {
            $this->applicableDiscountsList = [];
            if ($idList = $this->getApplicableDiscountsIdList()) {
                foreach ($idList as &$id) {
                    if ($discount = $this->getDiscount($id)) {
                        $this->applicableDiscountsList[] = $discount;
                    }
                }
            }
        }
        return $this->applicableDiscountsList;
    }

    /**
     * Returns a list of all discounts ids having discounted products at the moment.
     *
     * @return int[]
     */
    public function getApplicableDiscountsIdList()
    {
        $discountsIdList = [];
        foreach ($this->discountsList as $discount) {
            if ($discount->hasApplicableProductsIds()) {
                $discountsIdList[] = $discount->id;
            }
        }
        return $discountsIdList;
    }

    /**
     * Returns a list of all discounts (without products too).
     *
     * @return int[]
     */
    public function getDiscountsIdList()
    {
        $discountsIdList = [];
        foreach ($this->discountsList as $discount) {
            if ($discount->isApplicable()) {
                $discountsIdList[] = $discount->id;
            }
        }
        return $discountsIdList;
    }

    /**
     * @param int $id
     * @return bool|ShoppingBasketDiscount
     */
    public function getDiscount($id)
    {
        $discount = false;
        if (isset($this->discountsIndex[$id])) {
            $discount = $this->discountsIndex[$id];
        }
        return $discount;
    }

    public function calculateProductsListDiscounts(
        $productsList,
        $selectedDeliveryTypeId,
        $deliveryPrice,
        $productsPrice,
        $totalPrice
    ) {
        $updatedTotalPrice = $totalPrice;
        $this->appliedDiscountsList = [];
        $this->discountAmountsIndex = [];
        if ($discountsList = $this->getDiscountsList()) {
            foreach ($discountsList as $discount) {
                if (!isset($this->discountAmountsIndex[$discount->id])) {
                    $this->discountAmountsIndex[$discount->id] = 0;
                }
                $discount->setProductsPrice($productsPrice);
                $discount->setDeliveryPrice($deliveryPrice);
            }

            foreach ($productsList as &$product) {
                $productDiscount = $this->getProductDiscount($product->productId, $product->totalPrice, null, null, $product->amount);
                $product->discount = $productDiscount / $product->amount;
                $updatedTotalPrice -= $productDiscount;
            }
            //calculate delivery discount size
            $biggestDiscountAmount = null;
            $biggestDiscountId = false;
            $smallestDiscountAmount = null;
            $smallestDiscountId = false;
            $cooperateDiscountAmount = 0;
            $useSmallest = 0;

            //check all discounts to be able to decide after what should be used
            foreach ($discountsList as $discount) {
                if ($discountAmount = $discount->getDeliveryDiscountAmount($selectedDeliveryTypeId, $deliveryPrice, $productsList)) {
                    //do we have at least one discount with "useSmallest" discount?
                    if ($discount->groupBehaviour == 'useSmallest') {
                        $useSmallest = true;
                    } elseif ($discount->groupBehaviour == 'cooperate') { //find sum of all "cooperate" discounts
                        $cooperateDiscountAmount += $discountAmount;
                    } else {
                        //is it the biggest discount?
                        if ($biggestDiscountAmount === null || $discountAmount > $biggestDiscountAmount) {
                            $biggestDiscountAmount = $discountAmount;
                            $biggestDiscountId = $discount->id;
                        }
                    }

                    //is it the smallest discount?
                    if ($smallestDiscountAmount === null || $discountAmount < $smallestDiscountAmount) {
                        $smallestDiscountAmount = $discountAmount;
                        $smallestDiscountId = $discount->id;
                    }
                }
            }

            //if we have at least on "useSmallest" discount, then apply the smallest discount
            if ($useSmallest) {
                $deliveryDiscountAmount = $smallestDiscountAmount;
                $this->discountAmountsIndex[$smallestDiscountId] = $smallestDiscountAmount;
            } //then check if we have some "dominateSmaller" discount which is bigger than all others and/or sum of "cooperate" discounts
            elseif ($biggestDiscountAmount > $cooperateDiscountAmount) {
                $deliveryDiscountAmount = $biggestDiscountAmount;
                $this->discountAmountsIndex[$biggestDiscountId] = $biggestDiscountAmount;
            } //we have only "cooperate" discounts, lets use their sum and mark them all as active
            else {
                $deliveryDiscountAmount = $cooperateDiscountAmount;
                foreach ($discountsList as $discount) {
                    if ($discount->groupBehaviour == 'cooperate' && ($discountAmount = $discount->getDeliveryDiscountAmount($selectedDeliveryTypeId, $deliveryPrice, $productsList))) {
                        $this->discountAmountsIndex[$discount->id] = $discountAmount;
                    }
                }
            }
            if ($deliveryDiscountAmount > $deliveryPrice) {
                $deliveryDiscountAmount = $deliveryPrice;
            }

            $updatedTotalPrice -= $deliveryDiscountAmount;

            foreach ($discountsList as $discount) {
                if (isset($this->discountAmountsIndex[$discount->id])) {
                    $discount->setAllDiscountsAmount($this->discountAmountsIndex[$discount->id]);
                } else {
                    $discount->setAllDiscountsAmount(0);
                }
            }
            foreach ($this->discountAmountsIndex as $id => &$amount) {
                if ($discount = $this->getDiscount($id)) {
                    $discount->setAllDiscountsAmount($amount);
                }
            }

            foreach ($discountsList as $discount) {
                if ($discount->active) {
                    $this->appliedDiscountsList[] = $discount;
                }
            }
        }
        $this->totalPrice = $updatedTotalPrice;
    }

    public function getProductDiscount(
        $productId,
        $productTotalPrice,
        $relatedCategoriesIds = null,
        $relatedBrandsIds = null,
        $productAmount = 1
    ) {
        $productDiscount = 0;
        if ($discountsList = $this->getDiscountsList()) {
            //calculate product discount size
            $biggestDiscountAmount = null;
            $biggestDiscountId = false;
            $smallestDiscountAmount = null;
            $smallestDiscountId = false;
            $cooperateDiscountAmount = 0;
            $useSmallest = 0;

            //check all discounts to be able to decide after what should be used
            foreach ($discountsList as $discount) {
                if (($discountAmount = $discount->getProductDiscountAmount($productId, $productTotalPrice, $productAmount)) !== false) {
                    //do we have at least one discount with "useSmallest" discount?
                    if ($discount->groupBehaviour == 'useSmallest') {
                        $useSmallest = true;
                    } elseif ($discount->groupBehaviour == 'cooperate') { //find sum of all "cooperate" discounts
                        $cooperateDiscountAmount += $discountAmount;
                    } else {
                        //is it the biggest discount?
                        if ($biggestDiscountAmount === null || $discountAmount > $biggestDiscountAmount) {
                            $biggestDiscountAmount = $discountAmount;
                            $biggestDiscountId = $discount->id;
                        }
                    }

                    //is it the smallest discount?
                    if ($smallestDiscountAmount === null || $discountAmount < $smallestDiscountAmount) {
                        $smallestDiscountAmount = $discountAmount;
                        $smallestDiscountId = $discount->id;
                    }
                }
            }

            //if we have at least on "useSmallest" discount, then apply the smallest discount
            if ($useSmallest) {
                $productDiscount = $smallestDiscountAmount;
                if (isset($this->discountAmountsIndex[$smallestDiscountId])) {
                    $this->discountAmountsIndex[$smallestDiscountId] += $smallestDiscountAmount;
                }
            } //then check if we have some "dominateSmaller" discount which is bigger than all others and/or sum of "cooperate" discounts
            elseif ($biggestDiscountAmount > $cooperateDiscountAmount) {
                $productDiscount = $biggestDiscountAmount;
                if (isset($this->discountAmountsIndex[$biggestDiscountId])) {
                    $this->discountAmountsIndex[$biggestDiscountId] += $biggestDiscountAmount;
                }
            } //we have only "cooperate" discounts, lets use their sum and mark them all as active
            else {
                $productDiscount = $cooperateDiscountAmount;
                foreach ($discountsList as $discount) {
                    if ($discount->groupBehaviour == 'cooperate' && ($discountAmount = $discount->getProductDiscountAmount($productId, $productTotalPrice)) && isset($this->discountAmountsIndex[$discount->id])) {
                        $this->discountAmountsIndex[$discount->id] += $discountAmount;
                    }
                }
            }
            if ($productDiscount > $productTotalPrice) {
                $productDiscount = $productTotalPrice;
            }
        }
        return $productDiscount;
    }
}

class ShoppingBasketDiscount extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $storageData;
    public $id;
    public $title;
    public $code;
    public $startDate;
    public $endDate;
    public $promoCode;
    public $conditionPrice;
    public $conditionPriceMax;
    public $conditionUserGroupId;
    public $discountDelivery;
    public $discountProducts;
    public $discountedProductsIds;
    public $discountedCategoriesIds;
    public $discountedBrandIds;
    public $targetAllProducts;
    public $groupBehaviour;
    public $productDiscount; // discount amount
    public $deliveryTypesDiscountsIndex;
    public $showInBasket;
    public $basketText;
    public $displayText;
    public $displayProductsInBasket = false;
    public $active = false;
    public $appliedLast = true;
    // results
    protected $productsPrice = 0;
    protected $deliveryPrice = 0;
    protected $productsDiscountsIndex = [];
    protected $deliveryDiscount = 0;
    protected $generalDiscount = 0;
    protected $allDiscountsAmount = 0;
    protected $applicableProductIdIndex;
    protected $deepDiscountedCategoriesIds;
    protected $fixedPrice = 0;
    /**
     * @var shoppingBasketDiscounts
     */
    protected $shoppingBasketDiscounts;

    /**
     * @param $discountData - all data for this discount
     * @param shoppingBasketDiscounts $shoppingBasketDiscounts
     */
    public function __construct($discountData, shoppingBasketDiscounts $shoppingBasketDiscounts)
    {
        $this->storageData = $discountData;
        $this->shoppingBasketDiscounts = $shoppingBasketDiscounts;

        $this->id = $discountData['id'];
        $this->title = $discountData['title'];
        $this->code = $discountData['code'];
        $this->logic = $discountData['logic'];
        $this->startDate = $discountData['startDate'];
        $this->endDate = $discountData['endDate'];
        $this->promoCode = $discountData['promoCode'];
        $this->conditionPrice = $discountData['conditionPrice'];
        $this->conditionPriceMax = $discountData['conditionPriceMax'];
        $this->conditionUserGroupId = $discountData['conditionUserGroupId'];

        $this->discountedProductsIds = $discountData['discountedProductsIds'];
        $this->discountedCategoriesIds = $discountData['discountedCategoriesIds'];
        !isset($discountData['discountedBrandIds']) or $this->discountedBrandIds = $discountData['discountedBrandIds'];

        $this->targetAllProducts = $discountData['targetAllProducts'];
        $this->groupBehaviour = $discountData['groupBehaviour'];
        $this->productDiscount = $discountData['productDiscount'];
        $this->deliveryTypesDiscountsIndex = $discountData['deliveryTypesDiscountsIndex'];
        $this->fixedPrice = $discountData['fixedPrice'];

        $this->showInBasket = $discountData['showInBasket'];
        $this->basketText = $discountData['basketText'];
        $this->displayText = true;
        $this->displayProductsInBasket = $discountData['displayProductsInBasket'];
    }

    /**
     * Sets the price of currently selected basket delivery
     * @param $deliveryPrice
     */
    public function setDeliveryPrice($deliveryPrice)
    {
        $this->deliveryPrice = $deliveryPrice;
    }

    /**
     * Sets the total price of all currently added basket products
     *
     * @param $productsPrice
     */
    public function setProductsPrice($productsPrice)
    {
        $this->productsPrice = $productsPrice;
    }

    /**
     * Checks whether this discount has active products to be displayed.
     * Doesn't always means that the product is already discounted, but also includes potentially discountable products according to current user
     *
     * @return bool
     */
    public function hasApplicableProductsIds()
    {
        $result = false;
        if ($this->isApplicable()) {
            if ($this->discountedProductsIds || $this->discountedCategoriesIds || $this->discountedBrandIds || $this->targetAllProducts) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Check whether any of the products from a productsList are applicable for this discount
     *
     * @param $productsList
     * @return bool
     */
    public function checkProductsListIfApplicable($productsList)
    {
        if ($this->isApplicable()) {
            if ($this->targetAllProducts) {
                return true;
            }
            if (count(array_intersect($this->getApplicableProductsIds(), $productsList)) != 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the list of all directly connected products with all products of all connected categories.
     * Only returns product ids for potentially affected products according to current user
     *
     * @return array - id list of products
     */
    public function getApplicableProductsIds()
    {
        $productsIds = [];
        if ($this->isApplicable()) {
            $productsIds = $this->discountedProductsIds;
            $linksManager = $this->getService('linksManager');
            foreach ($this->discountedCategoriesIds as &$categoryId) {
                if ($categoryProductsIds = $linksManager->getConnectedIdList($categoryId, 'catalogue', 'parent')) {
                    $productsIds = array_merge($productsIds, $categoryProductsIds);
                }
            }
            if ($this->discountedBrandIds) {
                foreach ($this->discountedBrandIds as &$categoryId) {
                    if ($brandProductsIds = $linksManager->getConnectedIdList($categoryId, 'productbrand', 'parent')) {
                        $productsIds = array_merge($productsIds, $brandProductsIds);
                    }
                }
            }
        }

        return array_unique($productsIds);
    }

    /**
     * Returns the amount of discount for provided product.
     *
     * @param $productId
     * @param $productTotalPrice - full price of product
     * @return float|int
     */
    public function getProductDiscountAmount(
        $productId,
        $productTotalPrice,
        $productAmount = 1
    ) {
        $result = false;
        if ($this->isActive()) {
            if ($this->isProductDiscountable($productId)) {
                $result = $this->calculateDiscountAmount($this->productDiscount, $productTotalPrice, $productAmount);
            }
        }
        return $result;
    }

    /**
     * Returns the amount of discount for provided delivery type according to products list.
     * Delivery discount can depend on selected products in some cases, so products list is required.
     *
     * @param $deliveryId
     * @param $deliveryPrice - full price of delivery
     * @param $productsList - products ids from basket to find out whether this delivery discount is applied according to them
     * @return float|int
     */
    public function getDeliveryDiscountAmount($deliveryId, $deliveryPrice, $productsList)
    {
        $result = 0;
        if (isset($this->deliveryTypesDiscountsIndex[$deliveryId])) {
            $this->deliveryDiscount = $this->deliveryTypesDiscountsIndex[$deliveryId];
        } else {
            $this->deliveryDiscount = 0;
        }
        if ($this->isActive()) {
            if ($deliveryId && !empty($this->deliveryTypesDiscountsIndex[$deliveryId]) && $this->isDeliveryDiscountable($productsList)
            ) {
                $result = $this->calculateDiscountAmount($this->deliveryTypesDiscountsIndex[$deliveryId], $deliveryPrice);
            }
        }
        return $result;
    }

    /**
     * Check whether delivery type is subject to discount for all products from a list.
     *
     * @param $productsList
     * @return bool
     */
    protected function isDeliveryDiscountable($productsList)
    {
        $result = false;
        if ($this->targetAllProducts) {
            $result = true;
        } elseif ($productsList) {
            $allProductsAreDiscountable = true;
            foreach ($productsList as &$product) {
                if (!$this->isProductDiscountable($product->productId)) {
                    $allProductsAreDiscountable = false;
                    break;
                }
            }
            $result = $allProductsAreDiscountable;
        }
        return $result;
    }

    /**
     * @param $productId
     * @param $relatedCategoriesIds - DEPRECATED
     * @param $relatedBrandsIds - DEPRECATED
     * @return bool
     */
    public function isProductDiscountable($productId, $relatedCategoriesIds = null, $relatedBrandsIds = null)
    {
        if ($relatedCategoriesIds || $relatedBrandsIds) {
            $this->logError('Deprecated arguments passed for isProductDiscountable. ProductId:' . $productId);
        }

        $result = false;
        if ($this->targetAllProducts) {
            $result = true;
        } else {
            if ($this->applicableProductIdIndex === null) {
                $this->applicableProductIdIndex = [];

                $conditions = [];
                if ($this->discountedCategoriesIds && $this->discountedBrandIds) {
                    $conditions[] = [
                        'type',
                        'in',
                        ['catalogue', 'productbrand'],
                    ];
                    $conditions[] = [
                        'parentStructureId',
                        'in',
                        array_merge(
                            $this->getDeepDiscountedCategoriesIds(),
                            $this->discountedBrandIds
                        ),
                    ];
                } else {
                    if ($this->discountedCategoriesIds) {
                        $conditions[] = [
                            'type',
                            '=',
                            'catalogue',
                        ];
                        $conditions[] = [
                            'parentStructureId',
                            'in',
                            $this->getDeepDiscountedCategoriesIds(),
                        ];
                    } elseif ($this->discountedBrandIds) {
                        $conditions[] = [
                            'type',
                            '=',
                            'productbrand',
                        ];
                        $conditions[] = [
                            'parentStructureId',
                            'in',
                            $this->discountedBrandIds,
                        ];
                    }
                }

                if ($conditions) {
                    $collection = persistableCollection::getInstance('structure_links');

                    if ($records = $collection->conditionalLoad(['childStructureId'], $conditions)) {
                        foreach ($records as &$record) {
                            $this->applicableProductIdIndex[$record['childStructureId']] = true;
                        }
                    }
                }
                if ($this->discountedProductsIds) {
                    foreach ($this->discountedProductsIds as &$id) {
                        $this->applicableProductIdIndex[$id] = true;
                    }
                }
            }
            if (isset($this->applicableProductIdIndex[$productId])) {
                $result = true;
            }
        }
        return $result;
    }

    protected function getDeepDiscountedCategoriesIds()
    {
        if ($this->deepDiscountedCategoriesIds === null) {
            $this->deepDiscountedCategoriesIds = [];
            if ($this->discountedCategoriesIds) {
                $linksManager = $this->getService('linksManager');
                $this->deepDiscountedCategoriesIds = $this->discountedCategoriesIds;
                do {
                    foreach ($linksManager->getElementsLinks(current($this->deepDiscountedCategoriesIds), 'structure', 'parent', false) as $link) {
                        $this->deepDiscountedCategoriesIds[] = $link->childStructureId;
                    }
                } while (next($this->deepDiscountedCategoriesIds));

                $this->deepDiscountedCategoriesIds = array_unique($this->deepDiscountedCategoriesIds);
            }
        }
        return $this->deepDiscountedCategoriesIds;
    }

    /**
     * Check whether discount is potentially applicable to current user in present or in future.
     *
     * @return bool
     */
    public function isApplicable()
    {
        if ($this->isInPast() || !$this->acceptsCurrentUser() || $this->promoCodeRequired()) {
            return false;
        }
        return true;
    }

    /**
     * Check whether discount is active for this user, present time and current basket contents
     *
     * @return bool
     */
    protected function isActive()
    {
        $active = true;
        if (!$this->isApplicable()) {
            $active = false;
        } elseif ($this->isInFuture()) {
            $active = false;
        } elseif ($this->productsPrice < $this->conditionPrice || $this->conditionPriceMax > 0 && $this->productsPrice > $this->conditionPriceMax) {
            $active = false;
        }
        return $active;
    }

    public function isUsed()
    {
        return $this->isActive();
    }

    /**
     * Check whether discount is active for this user
     *
     * @return bool
     */
    public function acceptsCurrentUser()
    {
        $relevant = true;
        if ($this->conditionUserGroupId) {
            $user = $this->getService('user');
            $groups = $user->getGroupsIdList();
            if (!in_array($this->conditionUserGroupId, $groups)) {
                $relevant = false;
            }
        }
        return $relevant;
    }

    /**
     * Check whether discount requires promo code inserting in shopping basket and user has done it already
     *
     * @return bool
     */
    public function promoCodeRequired()
    {
        $required = false;
        if ($this->promoCode) {
            if ($this->promoCode != $this->shoppingBasketDiscounts->getCurrentPromoCode()) {
                $required = true;
            }
        }
        return $required;
    }

    public function acceptsPromoCode()
    {
        if ($this->promoCode && !$this->isInPast() && $this->acceptsCurrentUser()) {
            return true;
        }
        return false;
    }

    /**
     * Check whether discount is active because user has valid promo code active
     *
     * @return bool
     */
    public function hasActivePromoCode()
    {
        if ($this->promoCode) {
            if ($this->promoCode == $this->shoppingBasketDiscounts->getCurrentPromoCode()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $discountValue
     * @param $price
     * @return float|int
     */
    protected function calculateDiscountAmount($discountValue, $price, $productsAmount = 1)
    {
        $discountAmount = false;
        if ($this->fixedPrice) {
            $discountAmount = max(0, $price - $this->fixedPrice * $productsAmount);
        } else {
            if ($discountValue) {
                if (stripos($discountValue, '%')) {
                    $discountAmount = $price * floatval($discountValue) / 100;
                } else {
                    $discountAmount = $productsAmount * $discountValue;
                }
            }
        }
        return $discountAmount;
    }

    /**
     * Check whether discount was active in past and is not active in present
     *
     * @return bool
     */
    public function isInPast()
    {
        $currentTime = time();
        if ($this->endDate && ($currentTime >= $this->endDate)) {
            return true;
        }
        return false;
    }

    /**
     * Check whether discount will be active in future and is not active in present
     *
     * @return bool
     */
    protected function isInFuture()
    {
        $currentTime = time();
        if ($this->startDate && ($currentTime < $this->startDate)) {
            return true;
        }
        return false;
    }

    /**
     * @param $allDiscountsAmount
     */
    public function setAllDiscountsAmount($allDiscountsAmount)
    {
        $this->allDiscountsAmount = $allDiscountsAmount;
        if ($this->allDiscountsAmount > 0) {
            $this->active = true;
        } else {
            $this->active = false;
        }
    }

    /**
     * Returns overall value of this discount, including product and delivery discounts
     *
     * @param bool $convert
     * @return int|string
     */
    public function getAllDiscountsAmount($convert = true)
    {
        if ($convert) {
            return sprintf('%01.2f', $this->getService('CurrencySelector')->convertPrice($this->allDiscountsAmount));
        } else {
            return $this->allDiscountsAmount;
        }
    }
}