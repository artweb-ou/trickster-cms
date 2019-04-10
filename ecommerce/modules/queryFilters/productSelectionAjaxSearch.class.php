<?php

class productSelectionAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'productSelection';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}