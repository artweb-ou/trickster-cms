<?php

class campaignsListSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'campaignsList';
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