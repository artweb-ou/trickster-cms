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

    public function getData($productId)
    {
        //load all combination prices from values combination tables
        $records = $this->queryDb()
            ->select('combo', 'price')
            ->where('productId', '=', $productId)
            ->get();
        foreach ($records as $record) {
            $price = $record['price'];
            $result[$record['combo']] = $price;
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