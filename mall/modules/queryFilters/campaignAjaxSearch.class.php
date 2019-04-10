<?php

class campaignAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'campaign';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}