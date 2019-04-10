<?php

class productParameterAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'productParameter';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}