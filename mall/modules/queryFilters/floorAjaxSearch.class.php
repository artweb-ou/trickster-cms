<?php

class floorAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'floor';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}