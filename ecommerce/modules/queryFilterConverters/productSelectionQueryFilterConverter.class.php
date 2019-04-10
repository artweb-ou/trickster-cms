<?php

class productSelectionQueryFilterConverter extends queryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTableName()
    {
        return 'module_product_selection';
    }
}