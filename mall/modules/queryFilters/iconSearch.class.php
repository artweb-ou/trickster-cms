<?php

class iconSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'icon';
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