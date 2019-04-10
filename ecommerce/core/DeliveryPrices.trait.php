<?php

trait DeliveryPricesTrait
{
    protected $pricesIndex;
    protected $inactiveDeliveryTypesRecords;

    public function getPricesIndex()
    {
        if (is_null($this->pricesIndex)) {
            $pricesCollection = persistableCollection::getInstance('delivery_type_price');
            $searchFields = ['targetId' => $this->id];
            $pricesList = $pricesCollection->load($searchFields);
            $this->pricesIndex = [];
            foreach ($pricesList as &$priceRecord) {
                $this->pricesIndex[$priceRecord->deliveryTypeId][$priceRecord->regionId] = $priceRecord;
            }
        }
        return $this->pricesIndex;
    }

    public function getDeliveryPriceExtra($deliveryTypeId, $regionId = false)
    {
        $priceIndex = $this->getPricesIndex();
        if (isset($priceIndex[$deliveryTypeId][$regionId])) {
            return $priceIndex[$deliveryTypeId][$regionId]->price;
        }
        return '';
    }

    public function getDisabledDeliveryTypesRecords()
    {
        if (is_null($this->inactiveDeliveryTypesRecords)) {
            $this->inactiveDeliveryTypesRecords = [];
            $collection = persistableCollection::getInstance('delivery_type_inactive');
            $searchFields = ['targetId' => $this->id];
            $records = $collection->load($searchFields);
            foreach ($records as &$record) {
                $this->inactiveDeliveryTypesRecords[$record->deliveryTypeId] = $record;
            }
        }
        return $this->inactiveDeliveryTypesRecords;
    }

    public function getFormDeliveries()
    {
        $structureManager = $this->getService('structureManager');
        $formDeliveries = [];
        if ($deliveryTypesElement = $structureManager->getElementByMarker('deliveryTypes')) {
            $inactiveDeliveriesRecords = $this->getDisabledDeliveryTypesRecords();

            if ($formDeliveries = $structureManager->getElementsChildren($deliveryTypesElement->id)) {
                foreach ($formDeliveries as &$deliveryTypeElement) {
                    $deliveryTypeElement->active = !isset($inactiveDeliveriesRecords[$deliveryTypeElement->id]);
                }
            }
        }

        return $formDeliveries;
    }

    public function receiveDeliveryData()
    {
        //persist delivery prices
        $pricesIndex = $this->getPricesIndex();
        $inactiveDeliveriesRecords = $this->getDisabledDeliveryTypesRecords();

        foreach ($this->formDeliveries as $deliveryId => $regions) {
            foreach ($regions as $regionId => $deliveryData) {
                $deliveryPrice = $deliveryData["priceExtra"];
                if ($deliveryPrice !== '') {
                    if (isset($pricesIndex[$deliveryId][$regionId])) {
                        $priceObject = $pricesIndex[$deliveryId][$regionId];
                        unset($pricesIndex[$deliveryId][$regionId]);
                    } else {
                        $priceObject = persistableCollection::getInstance('delivery_type_price')->getEmptyObject();
                        $priceObject->targetId = $this->id;
                        $priceObject->regionId = $regionId;
                        $priceObject->deliveryTypeId = $deliveryId;
                    }
                    $priceObject->price = $deliveryPrice;
                    $priceObject->persist();
                }
                // update delivery type status
                if (!$regionId) {
                    $deliveryTypeDisabled = isset($inactiveDeliveriesRecords[$deliveryId]);
                    if ($deliveryData['active']) {
                        if ($deliveryTypeDisabled) {
                            $inactiveDeliveriesRecords[$deliveryId]->delete();
                        }
                    } elseif (!$deliveryTypeDisabled) {
                        $deliveryInactiveObject = persistableCollection::getInstance('delivery_type_inactive')
                            ->getEmptyObject();
                        $deliveryInactiveObject->targetId = $this->id;
                        $deliveryInactiveObject->deliveryTypeId = $deliveryId;
                        $deliveryInactiveObject->persist();
                    }
                }
            }
        }
        //delete records with price=0
        foreach ($pricesIndex as $regionData) {
            foreach ($regionData as $priceRecord) {
                $priceRecord->delete();
            }
        }
    }
}