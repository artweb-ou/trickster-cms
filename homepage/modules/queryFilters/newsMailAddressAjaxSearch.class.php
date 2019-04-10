<?php

class newsMailAddressAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'newsMailAddress';
    }

    protected function getTitleFieldNames()
    {
        return ['personalName', 'email'];
    }
}