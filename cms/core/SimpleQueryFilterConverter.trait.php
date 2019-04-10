<?php

trait SimpleQueryFilterConverterTrait
{
    public function convert($sourceData, $sourceType)
    {
        $query = $this->getService('db')->table($this->getTableName())->select('id')->distinct();
        return $query;
    }

    protected function getTableName()
    {
        return 'module_' . strtolower($this->getType());
    }

    abstract function getType();
}