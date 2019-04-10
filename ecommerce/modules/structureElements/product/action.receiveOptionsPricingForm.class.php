<?php

class receiveOptionsPricingFormProduct extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $pricesManager = $this->getService('ProductOptionsPricesManager');
            $existingInfo = $structureElement->getSelectionsPricingsMap();
            $records = [];
            $unchangedCombos = [];
            $input = $structureElement->optionsPricingInput;
            foreach ($input as $combo => $price) {
                $price = trim($price);
                $price = str_replace([" ", ','], ["", '.'], $price);
                if (is_numeric($price) === false) {
                    continue;
                }
                $options = array_filter(explode(';', $combo));
                $combo = $structureElement->generateOptionsComboCode($options); // ensure options order
                if (isset($existingInfo[$combo]) && $price == $existingInfo[$combo]) {
                    $unchangedCombos[] = $combo;
                    continue;
                }
                $records[] = [
                    'productId' => $structureElement->id,
                    'combo' => $combo,
                    'price' => $price,
                ];
            }
            $cleanupQuery = $pricesManager->queryDb()->where('productId', '=', $structureElement->id);
            if ($unchangedCombos) {
                $cleanupQuery->whereNotIn('combo', $unchangedCombos);
            }
            $cleanupQuery->delete();
            if ($records) {
                $pricesManager->insertRecords($records);
            }
            $url = $structureElement->URL . 'id:' . $structureElement->id
                . '/action:showOptionsPricingForm/';
            $controller->redirect($url);
        }
        $structureElement->executeAction("showOptionsPricingForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'optionsPricingInput',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}
