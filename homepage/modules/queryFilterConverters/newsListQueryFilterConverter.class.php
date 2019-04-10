<?php

class newsListQueryFilterConverter extends queryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTableName()
    {
        return 'module_newslist';
    }
}