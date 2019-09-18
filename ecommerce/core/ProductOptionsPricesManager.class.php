<?php

class ProductOptionsPricesManager extends errorLogger
{
    /**
     * @var \Illuminate\Database\Connection
     */
    protected $db;
    /**
     * @var ParametersManager
     */
    protected $parametersManager;

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function setParametersManager($parametersManager)
    {
        $this->parametersManager = $parametersManager;
    }

    public function getData($productId, $productPrice)
    {
        $result = [];

        //load all additional prices for all selection value
        $optionAdditionalPricesIndex = [];
        if ($basketSelections = $this->parametersManager->getProductBasketSelectionsInfo($productId)) {
            foreach ($basketSelections as $selection) {
                foreach ($selection['productOptions'] as $option) {
                    if ($option['price'] > 0) {
                        $optionAdditionalPricesIndex[$option['id']] = $option['price'];
                    }
                }
            }
        }

        //load all combination prices from values combination tables
        $records = $this->queryDb()
            ->select('combo', 'price')
            ->where('productId', '=', $productId)
            ->get();
        foreach ($records as $record) {
            $price = $record['price'];
            $result[$record['combo']] = $price;
        }

        //mix all combinations with all additional prices
        foreach ($optionAdditionalPricesIndex as $additionalOptionId => $additionalPrice) {
            $additionalCombos = [];
            //first make a new separated combination for this single option
            $additionalCombos[$additionalOptionId . ';'] = $productPrice + $additionalPrice;

            //now check all combination table prices, and generate a new combo for all additional prices
            foreach ($result as $combo => $price) {
                $optionIds = explode(';', $combo);
                //throw out empty string
                $optionIds = array_filter($optionIds);
                //if we have a combo with this option already, no need to generate a new one, just add a price
                if (in_array($additionalOptionId, $optionIds)) {
                    $result[$combo] += $additionalPrice;
                } else {
                    //this combination doesn't use this option, let's add a new combo for this
                    $optionIds[] = $additionalOptionId;
                    //option ids combo string should be sorted by id ASC
                    sort($optionIds, SORT_ASC);
                    //wrtie additional combination
                    $additionalCombos[implode(';', $optionIds).';'] = $price + $additionalPrice;
                }
            }
            //now add the combinations to main result
            $result = array_merge($result, $additionalCombos);
        }

        return $result;
    }

    public function insertRecords($records)
    {
        return $this->queryDb()->insert($records);
    }

    public function deleteExisting($productId)
    {
        return $this->queryDb()
            ->where('productId', '=', $productId)
            ->delete();
    }

    public function queryDb()
    {
        return $this->db->table('module_product_selection_pricing');
    }
}