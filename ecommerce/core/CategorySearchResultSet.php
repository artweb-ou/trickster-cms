<?php

/**
 * Class CategorySearchResultSet
 *
 * @property categoryElement[] $elements
 */
class CategorySearchResultSet extends SearchResultSet
{

    public function getSubCount()
    {
        $subCount = 0;
        foreach ($this->elements as $element) {
            $subCount += $element->getProductsListBaseAmount();
        }
        return $subCount;
    }
}