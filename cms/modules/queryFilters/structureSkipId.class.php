<?php

class structureSkipIdQueryFilter extends queryFilter
{
    public function getRequiredType()
    {
        return false;
    }

    public function getFilteredIdList($argument, $query)
    {
        $query->whereNotIn('id', (array)$argument);
        return $query;
    }
}