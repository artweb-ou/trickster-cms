<?php

class productCatalogueAjaxSearchQueryFilter extends ajaxSearchQueryFilter
{
    protected function getTypeName()
    {
        return 'productCatalogue';
    }

    protected function getTitleFieldNames()
    {
        return ['title'];
    }
}