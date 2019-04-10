<?php

class campaignSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'campaign';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }

    protected function getContentFieldNames()
    {
        return ['content'];
    }
}