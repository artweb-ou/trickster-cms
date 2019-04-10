<?php

class productAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'product';
    }

    protected function getTitleFieldNames()
    {
        return ['title', 'code'];
    }
}