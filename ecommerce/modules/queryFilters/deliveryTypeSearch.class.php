<?php

class deliveryTypeSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'deliveryType';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }

    protected function getContentFieldNames()
    {
        return false;
    }
}