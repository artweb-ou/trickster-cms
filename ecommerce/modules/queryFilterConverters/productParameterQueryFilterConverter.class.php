<?php

class productParameterQueryFilterConverter extends QueryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTable(): string
    {
        return 'module_product_parameter';
    }
}