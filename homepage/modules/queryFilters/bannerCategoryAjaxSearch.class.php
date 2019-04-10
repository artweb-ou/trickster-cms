<?php

class bannerCategoryAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'bannerCategory';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}