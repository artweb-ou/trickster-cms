<?php

class categoryAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'category';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}