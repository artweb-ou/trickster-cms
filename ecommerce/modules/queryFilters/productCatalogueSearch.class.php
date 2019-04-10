<?php

class productCatalogueSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'productCatalogue';
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