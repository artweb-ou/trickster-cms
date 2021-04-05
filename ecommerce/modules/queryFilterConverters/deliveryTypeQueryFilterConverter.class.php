<?php

class deliveryTypeQueryFilterConverter extends QueryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTable(): string
    {
        return 'module_delivery_type';
    }
}