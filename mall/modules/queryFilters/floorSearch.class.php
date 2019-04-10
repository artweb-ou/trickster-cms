<?php

class floorSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'floor';
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