<?php

class productSelectionQueryFilterConverter extends QueryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTable(): string
    {
        return 'module_product_selection';
    }
}