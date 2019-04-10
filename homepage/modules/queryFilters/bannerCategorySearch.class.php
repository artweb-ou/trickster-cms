<?php

class bannerCategorySearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'bannerCategory';
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