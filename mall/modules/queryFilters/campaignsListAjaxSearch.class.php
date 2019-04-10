<?php

class campaignsListAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'campaignsList';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}