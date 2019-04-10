<?php

class deliveryTypeElement extends structureElement
{
    public $dataResourceName = 'module_delivery_type';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $pricesIndex;
    protected $regionsPrices;
    protected $deliveryRegionsIds;
    protected $fieldsInfoIndex;
    protected $fieldsInfoList;
    protected $connectedPaymentMethods;
    protected $connectedPaymentMethodsIds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['prices'] = 'array';
        $moduleStructure['fields'] = 'array';
        $moduleStructure['code'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['extraType'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['paymentMethodsIds'] = 'numbersArray';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['calculationLogic'] = 'text'; //
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'content';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showPrices',
            'showFields',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getMinPrice()
    {
        $price = 0;
        if ($prices = $this->getRegionsPrices()) {
            $price = min($prices);
        }
        return $price;
    }

    public function getMaxPrice()
    {
        $price = 0;
        if ($prices = $this->getRegionsPrices()) {
            $price = max($prices);
        }
        return $price;
    }

    public function getRegionsPrices()
    {
        if ($this->regionsPrices == null) {
            $this->regionsPrices = [];
            if ($regionsIds = $this->getDeliveryRegionsIds()) {
                $collection = persistableCollection::getInstance('delivery_type_price');
                $conditions = [
                    [
                        'column' => 'deliveryTypeId',
                        'action' => '=',
                        'argument' => $this->id,
                    ],
                    [
                        'column' => 'targetId',
                        'action' => 'IN',
                        'argument' => $regionsIds,
                    ],
                ];
                if ($records = $collection->conditionalLoad('price', $conditions)) {
                    foreach ($records as &$record) {
                        $this->regionsPrices[] = $record['price'];
                    }
                }
            }
        }
        return $this->regionsPrices;
    }

    public function getCountriesList()
    {
        $pricesIndex = $this->getPricesIndex();
        $structureManager = $this->getService('structureManager');
        $countriesList = [];
        if ($deliveryCountries = $structureManager->getElementByMarker('deliveryCountries')) {
            $countriesList = $structureManager->getElementsChildren($deliveryCountries->id);

            foreach ($countriesList as &$country) {
                if ($country->citiesList = $structureManager->getElementsChildren($country->id)) {
                    foreach ($country->citiesList as $city) {
                        if (isset($pricesIndex[$city->id])) {
                            $city->selected = true;
                            $city->deliveryPrice = $pricesIndex[$city->id]->price;
                        } else {
                            $city->selected = false;
                        }
                    }
                }

                if (isset($pricesIndex[$country->id])) {
                    $country->selected = true;
                    $country->deliveryPrice = $pricesIndex[$country->id]->price;
                } else {
                    $country->selected = false;
                }
            }
        }
        return $countriesList;
    }

    public function getDeliveryRegionsIds()
    {
        if ($this->deliveryRegionsIds == null) {
            $this->deliveryRegionsIds = [];
            $collection = persistableCollection::getInstance('structure_elements');
            $conditions = [
                [
                    'column' => 'structureType',
                    'action' => 'IN',
                    'argument' => [
                        'deliveryCountry',
                        'deliveryCity',
                    ],
                ],
            ];
            $records = $collection->conditionalLoad('distinct(id)', $conditions, [], [], [], true);
            foreach ($records as &$record) {
                $this->deliveryRegionsIds[] = $record['id'];
            }
        }
        return $this->deliveryRegionsIds;
    }

    public function getPricesIndex()
    {
        if (is_null($this->pricesIndex)) {
            $pricesCollection = persistableCollection::getInstance('delivery_type_price');
            $searchFields = ['deliveryTypeId' => $this->id];
            $pricesList = $pricesCollection->load($searchFields);
            $this->pricesIndex = [];
            foreach ($pricesList as &$priceRecord) {
                $this->pricesIndex[$priceRecord->targetId] = $priceRecord;
            }
        }
        return $this->pricesIndex;
    }

    public function getFieldsIndex()
    {
        if (is_null($this->fieldsInfoIndex)) {
            $this->loadFieldsInfo();
        }
        return $this->fieldsInfoIndex;
    }

    public function getFieldsList()
    {
        if (is_null($this->fieldsInfoList)) {
            $this->loadFieldsInfo();
        }
        return $this->fieldsInfoList;
    }

    protected function loadFieldsInfo()
    {
        $fieldsCollection = persistableCollection::getInstance('delivery_type_field');
        $searchFields = ['deliveryTypeId' => $this->id];
        $this->fieldsInfoList = $fieldsCollection->load($searchFields);

        // sort
        if ($this->fieldsInfoList) {
            $structureManager = $this->getService('structureManager');
            $sort = [];
            $fieldsIds = [];
            foreach ($this->fieldsInfoList as $fieldInfo) {
                $fieldsIds[] = $fieldInfo->fieldId;
            }
            $basketFieldsElementId = $structureManager->getElementIdByMarker("basketFields");
            $linksCollection = persistableCollection::getInstance('structure_links');
            $conditions = [
                [
                    "parentStructureId",
                    "=",
                    $basketFieldsElementId,
                ],
                [
                    "childStructureId",
                    "IN",
                    $fieldsIds,
                ],
            ];
            if ($records = $linksCollection->conditionalLoad([
                "childStructureId",
                "position",
            ], $conditions)
            ) {
                $positionsIndex = [];
                foreach ($records as &$record) {
                    $positionsIndex[$record['childStructureId']] = $record['position'];
                }

                foreach ($fieldsIds as $fieldId) {
                    if ($positionsIndex[$fieldId]) {
                        $sort[] = $positionsIndex[$fieldId];
                    } else {
                        $sort[] = 0;
                    }
                }
                array_multisort($sort, SORT_ASC, $this->fieldsInfoList);
            }
        }
        $this->fieldsInfoIndex = [];
        foreach ($this->fieldsInfoList as &$fieldRecord) {
            $this->fieldsInfoIndex[$fieldRecord->fieldId] = $fieldRecord;
        }
    }

    public function linkWithElements($idsToLink, $linkType, $bidirectional = false)
    {
        $linksManager = $this->getService('linksManager');
        if ($linkedIds = $linksManager->getConnectedIdList($this->id, $linkType, "parent")) {
            foreach ($linkedIds as &$linkedId) {
                if (!in_array($linkedId, $idsToLink)) {
                    $linksManager->unLinkElements($this->id, $linkedId, $linkType);
                }
            }
            $idsToLink = array_diff($idsToLink, $linkedIds);
        }
        foreach ($idsToLink as &$idToLink) {
            $linksManager->linkElements($this->id, $idToLink, $linkType, $bidirectional);
        }
    }

    public function getConnectedPaymentMethods()
    {
        if (is_null($this->connectedPaymentMethods)) {
            $this->connectedPaymentMethods = [];
            if ($paymentMethodsIds = $this->getConnectedPaymentMethodsIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($paymentMethodsIds as &$paymentMethodId) {
                    if ($paymentMethodId && $method = $structureManager->getElementById($paymentMethodId)) {
                        $this->connectedPaymentMethods[] = $method;
                    }
                }
            }
        }
        return $this->connectedPaymentMethods;
    }

    public function getConnectedPaymentMethodsIds()
    {
        if (is_null($this->connectedPaymentMethodsIds)) {
            $this->connectedPaymentMethodsIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, "deliveryTypePaymentMethod", "parent");
        }
        return $this->connectedPaymentMethodsIds;
    }

    public function getAvailablePaymentMethods()
    {
        $paymentMethods = [];
        $collection = persistableCollection::getInstance("module_paymentmethod");
        if ($records = $collection->conditionalLoad('distinct(id)', [], ['id' => 'desc'], [], [], true)
        ) {
            $paymentMethodsIds = [];
            foreach ($records as &$record) {
                $paymentMethodsIds[] = $record["id"];
            }
            /**
             * @var structureElement[] $methods
             */
            $methods = $this->getService('structureManager')->getElementsByIdList($paymentMethodsIds);
            foreach ($methods as &$method) {
                $paymentMethods[] =
                    [
                        'id' => $method->id,
                        'title' => $method->getTitle(),
                    ];
            }
        }
        return $paymentMethods;
    }
}