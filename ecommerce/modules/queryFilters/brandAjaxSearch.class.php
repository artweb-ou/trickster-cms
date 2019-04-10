<?php

class brandAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'brand';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}