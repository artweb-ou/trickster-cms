<?php

class newsMailAddressSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'newsMailAddress';
    }

    protected function getTitleFieldNames()
    {
        return ['personalName', 'email'];
    }

    protected function getContentFieldNames()
    {
        return [];
    }
}