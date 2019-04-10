<?php

class discountSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'discount';
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