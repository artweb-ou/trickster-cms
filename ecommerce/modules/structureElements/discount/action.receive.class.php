<?php

class receiveDiscount extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if ($structureElement->code == '') {
                $structureElement->code = 'discount' . $structureElement->id;
            }
            if (!$structureElement->structureName) {
                $structureElement->structureName = $structureElement->title;
            }
            if ($structureElement->productDiscount < 0) {
                $structureElement->productDiscount = str_ireplace('-', '', $structureElement->productDiscount);
            }
            if ($deliveryTypesDiscountInfoForm = $structureElement->deliveryTypesDiscountInfoForm) {
                foreach ($deliveryTypesDiscountInfoForm as $id => &$deliveryTypeDiscountValue) {
                    if (!$deliveryTypeDiscountValue) {
                        unset($deliveryTypesDiscountInfoForm[$id]);
                    }
                }
                $structureElement->deliveryTypesDiscountInfo = serialize($deliveryTypesDiscountInfoForm);
            } else {
                $structureElement->deliveryTypesDiscountInfo = '';
            }

            foreach ($structureElement->getMultilanguageDataChunk("image") as $languageId => $image) {
                if ($image->originalName) {
                    $structureElement->setValue('image', $structureElement->id . '_' . $languageId, $languageId);
                    $structureElement->setValue('originalName', $image->originalName, $languageId);
                }
            }

            foreach ($structureElement->getMultilanguageDataChunk("icon") as $languageId => $icon) {
                if ($icon->originalName) {
                    $structureElement->setValue('icon', $structureElement->id . 'ico' . $languageId, $languageId);
                    $structureElement->setValue('iconOriginalName', $icon->originalName, $languageId);
                }
            }

            $structureElement->persistElementData();

            $linksManager = $this->getService('linksManager');

            $structureElement->updateConnectedProducts($structureElement->products);
            $structureElement->updateConnectedCategories($structureElement->categories);
            $structureElement->updateConnectedBrands($structureElement->brands);

            // check deliverytype links
            if ($connectedDeliveryTypeIds = $structureElement->getConnectedDeliveryTypesIds()) {
                foreach ($connectedDeliveryTypeIds as &$connectedDeliveryTypeId) {
                    if (!in_array($connectedDeliveryTypeId, $structureElement->deliveryTypes)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedDeliveryTypeId, 'discountDeliveryType');
                    }
                }
            }
            foreach ($structureElement->deliveryTypes as $selectedDeliveryTypeId) {
                $linksManager->linkElements($structureElement->id, $selectedDeliveryTypeId, 'discountDeliveryType');
            }

            // connect all discountslists configured to show all discounts
            if ($discountsLists = $structureManager->getElementsByType('discountsList')) {
                $linksManager = $this->getService('linksManager');
                foreach ($discountsLists as &$discountsList) {
                    if ($discountsList->connectAll) {
                        $linksManager->linkElements($discountsList->id, $structureElement->id, 'discountsList');
                    }
                }
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'code',
            'conditionPrice',
            'conditionPriceMax',
            'conditionUserGroupId',
            'startDate',
            'endDate',
            'promoCode',
            'products',
            'categories',
            'brands',
            'deliveryTypes',
            'productDiscount',
            'groupBehaviour',
            'targetAllProducts',
            'deliveryTypesDiscountInfoForm',
            // discount fields:
            'structureName',
            'content',
            'image',
            'icon',
            'link',
            //            'priceSortingEnabled',
            //            'nameSortingEnabled',
            //            'dateSortingEnabled',
            //            'parameterFilterEnabled',
            //            'availabilityFilterEnabled',
            //            'brandFilterEnabled',
            //            'parameterFilterEnabled',
            //            'amountOnPageEnabled',
            'fixedPrice',
            'showInBasket',
            'basketText',
            'displayProductsInBasket',
            'reference',
            'iconWidth',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

