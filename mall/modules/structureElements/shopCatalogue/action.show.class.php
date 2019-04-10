<?php

class showShopCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('content');
        if ($structureElement->requested) {
            $structureElement->categoriesList = $structureElement->getCategoriesList();

            $structureElement->columnsList = [
                0 => [],
                1 => [],
                2 => [],
            ];

            $shopsCount = 0;
            foreach ($structureElement->categoriesList as &$category) {
                if ($category->requested) {
                    $structureElement->requestedCategory = $category;
                }
                $category->columns = $structureElement->columns;
                $shopsCount += count($category->getShopsList());
            }

            $columnsCount = 3;
            $column = 0;
            $columnShopsCount = 0;
            foreach ($structureElement->categoriesList as &$category) {
                $categoryCount = count($category->getShopsList());
                if (($columnShopsCount + $categoryCount / 2 >= $shopsCount / $columnsCount) && $column != $columnsCount - 1) {
                    $column++;
                    $columnShopsCount = 0;
                }
                $columnShopsCount += $categoryCount;
                $structureElement->columnsList[$column][] = $category;
                if (($columnShopsCount >= $shopsCount / $columnsCount) && $column != $columnsCount - 1) {
                    $column++;
                    $columnShopsCount = 0;
                }
            }
        }
    }
}
