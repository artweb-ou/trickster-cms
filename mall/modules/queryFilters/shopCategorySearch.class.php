<?php

class shopCategorySearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'shopCategory';
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