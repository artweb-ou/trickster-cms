<?php

class ProductOptionsImagesManager extends errorLogger
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
            ->select('selectionValue', 'image')
            ->where('productId', '=', $productId)
            ->get();
        foreach ($records as $record) {
            $result[$record['selectionValue']] = $record['image'];
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
        return $this->db->table('module_product_selection_image');
    }
}