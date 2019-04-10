<?php

class categorySearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'category';
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