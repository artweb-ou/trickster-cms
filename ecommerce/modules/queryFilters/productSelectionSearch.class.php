<?php

class productSelectionSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'productSelection';
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