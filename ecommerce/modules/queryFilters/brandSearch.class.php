<?php

class brandSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'brand';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }

    protected function getContentFieldNames()
    {
        return ['content', 'introduction'];
    }
}