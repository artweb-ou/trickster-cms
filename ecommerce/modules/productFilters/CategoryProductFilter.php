<?php

class CategoryProductFilter extends ProductFilter
{
    protected $type = 'category';

    public function getOptionsInfo()
    {
        if ($this->optionsInfo === null) {
            $this->optionsInfo = [];
            if ($categories = $this->productsListElement->getProductsListCategories()) {
                $argumentMap = $this->getArguments();

                foreach ($categories as &$category) {
                    if (!$category->hidden) {
                        $this->optionsInfo[] = [
                            'title' => $category->title,
                            'selected' => isset($argumentMap[$category->id]) || $category->requested,
                            'id' => $category->id,
                            'url' => $category->URL,
                        ];
                    }
                }
            }
        }
        return $this->optionsInfo;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterCategoryIds());
    }
}