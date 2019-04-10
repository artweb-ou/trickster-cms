<?php

class productSelectionValueAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'productSelectionValue';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}