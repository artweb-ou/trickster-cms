<?php

class shopCategoryAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'shopCategory';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}