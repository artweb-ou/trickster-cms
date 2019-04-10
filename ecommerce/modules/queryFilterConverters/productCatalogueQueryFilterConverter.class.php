<?php

class productCatalogueQueryFilterConverter extends queryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTableName()
    {
        return 'module_catalogue_filter';
    }
}