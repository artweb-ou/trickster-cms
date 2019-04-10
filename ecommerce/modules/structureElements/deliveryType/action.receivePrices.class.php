<?php

class receivePricesDeliveryType extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->persistElementData();

            // update regions prices
            $pricesIndex = $structureElement->getPricesIndex();
            $pricesCollection = persistableCollection::getInstance('delivery_type_price');
            foreach ($structureElement->prices as $targetId => $deliveryPrice) {
                $deliveryPrice = str_replace(',', '.', $deliveryPrice);
                if (isset($pricesIndex[$targetId])) {
                    $priceObject = $pricesIndex[$targetId];
                    unset($pricesIndex[$targetId]);
                } else {
                    $priceObject = $pricesCollection->getEmptyObject();
                    $priceObject->targetId = $targetId;
                    $priceObject->deliveryTypeId = $structureElement->id;
                }
                $priceObject->price = $deliveryPrice;
                $priceObject->persist();
            }
            //delete obsolete records
            $regionsIds = $structureElement->getDeliveryRegionsIds();
            foreach ($pricesIndex as &$priceRecord) {
                // delete entries which are made for cities/regions or are 0
                if ($priceRecord->price == 0 || in_array($priceRecord->targetId, $regionsIds)) {
                    $priceRecord->delete();
                }
            }
            $controller->redirect($structureElement->getUrl('showPrices'));
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'prices',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['structureName'][] = 'notEmpty';
    }
}

