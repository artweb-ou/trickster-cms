<?php

class deliveryTypeQueryFilterConverter extends queryFilterConverter
{
    use SimpleQueryFilterConverterTrait;

    protected function getTableName()
    {
        return 'module_delivery_type';
    }
}