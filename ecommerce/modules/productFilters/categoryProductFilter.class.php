<?php

class categoryProductFilter extends productFilter
{
    protected $type = 'category';

    public function getOptionsInfo()
    {
        if ($this->options === null) {
            $this->options = [];
            if ($categories = $this->productsListElement->getProductsListCategories()) {
                $argumentMap = $this->getArguments();

                foreach ($categories as &$category) {
                    if (!$category->hidden) {
                        $this->options[] = [
                            'title' => $category->title,
                            'selected' => isset($argumentMap[$category->id]) || $category->requested,
                            'id' => $category->id,
                            'url' => $category->URL,
                        ];
                    }
                }
            }
        }
        return $this->options;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterCategoryIds());
    }
}