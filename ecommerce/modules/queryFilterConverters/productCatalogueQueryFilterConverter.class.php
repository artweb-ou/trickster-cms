<?php

class productCatalogueQueryFilterConverter extends QueryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTable(): string
    {
        return 'module_catalogue_filter';
    }
}