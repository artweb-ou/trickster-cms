<?php

class shopAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'shop';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}