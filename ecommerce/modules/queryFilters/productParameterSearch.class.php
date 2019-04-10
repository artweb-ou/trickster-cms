<?php

class productParameterSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'productParameter';
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