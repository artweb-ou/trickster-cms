<?php

class structureQueryFilterConverter extends QueryFilterConverter
{
    public function convert($sourceData, $sourceType)
    {
        $query = $this->getService('db')->table($this->getTableName())->select('id');
        return $query;
    }

    protected function getTableName()
    {
        return 'structure_elements';
    }
}