<?php

class categoryProductFilter extends productFilter
{
    protected $type = 'category';
    protected $optionsChildrenIds = [];

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
//    protected function limitOptions(array $productsIds)
//    {
//        if ($productsIds) {
//            $collection = persistableCollection::getInstance('structure_links');
//            $conditions = [
//                [
//                    'childStructureId',
//                    'IN',
//                    $productsIds,
//                ],
//                [
//                    'type',
//                    '=',
//                    "catalogue",
//                ],
//            ];
//            if ($records = $collection->conditionalLoad('distinct(parentStructureId)', $conditions, [], [], [], true)
//            ) {
//                $productsCategoriesIds = [];
//                foreach ($records as &$record) {
//                    $productsCategoriesIds[] = $record['parentStructureId'];
//                }
//                if (!$this->options) {
//                    $this->options = $productsCategoriesIds;
//                } else {
//                    $this->options = array_intersect($this->options, $productsCategoriesIds);
//                    foreach ($this->optionsChildrenIds as $key => &$childrenIds) {
//                        if (count(array_intersect($childrenIds, $productsCategoriesIds)) > 0) {
//                            $this->options[] = $key;
//                        }
//                    }
//                    $this->options = array_unique($this->options);
//                }
//            }
//        }
//    }


}