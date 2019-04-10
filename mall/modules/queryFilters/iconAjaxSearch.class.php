<?php

class iconAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'icon';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}