<?php

class deliveryTypeAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'deliveryType';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}