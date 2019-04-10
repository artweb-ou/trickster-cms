<?php

class shopSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'shop';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }

    protected function getContentFieldNames()
    {
        return ['content'];
    }
}