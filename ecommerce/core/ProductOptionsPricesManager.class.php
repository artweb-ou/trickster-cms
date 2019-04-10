<?php

class ProductOptionsPricesManager extends errorLogger
{
    protected $db;

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function getData($productId)
    {
        $result = [];
        $records = $this->queryDb()
            ->select('combo', 'price')
            ->where('productId', '=', $productId)
            ->get();
        foreach ($records as $record) {
            $result[$record['combo']] = $record['price'];
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