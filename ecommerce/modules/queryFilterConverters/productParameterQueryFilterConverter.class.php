<?php

class productParameterQueryFilterConverter extends queryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTableName()
    {
        return 'module_product_parameter';
    }
}