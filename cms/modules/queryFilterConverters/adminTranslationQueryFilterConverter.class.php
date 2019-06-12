<?php

class adminTranslationQueryFilterConverter extends queryFilterConverter
{
    public function convert($sourceData, $sourceType)
    {
        /**
         * @var \Illuminate\Database\Connection $db ;
         */
        $db = $this->getService('db');
        $query = $db
            ->table($this->getTableName())
            ->select('id')
            ->whereIn('id', function($query){
                $query
                    ->from('structure_elements')
                    ->where('structureType', '=', 'adminTranslation')
                    ->select('id');
            })
        ;
        return $query;
    }

    protected function getTableName()
    {
        return 'module_translation';
    }
}