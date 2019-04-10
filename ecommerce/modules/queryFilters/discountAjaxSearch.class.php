<?php

class discountAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'discount';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}