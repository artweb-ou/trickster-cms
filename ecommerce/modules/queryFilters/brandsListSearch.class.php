<?php

class brandsListSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'brandsList';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }

    protected function getContentFieldNames()
    {
        return [];
    }
}