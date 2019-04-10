<?php

interface importPluginElementInterface
{
    public function import();

    public function getOriginName();

    public function getName();

    public function getCategoriesIdentifiers();

    public function getCategoriesTemplate();

    public function getImportIdCategoryIdIndex();

    public function getCategoryIdByImportId($importId);

    /**
     * @return Warehouse
     */
    public function getWarehouse();
}