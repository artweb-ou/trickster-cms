<?php

class brandProductFilter extends productFilter
{
    protected $type = 'brand';

    public function getOptionsInfo()
    {
        if ($this->options === null) {
            $this->options = [];
            if ($brands = $this->productsListElement->getProductsListBrands()) {
                $argumentMap = $this->getArguments();

                foreach ($brands as &$brand) {
                    if (!$brand->hidden) {
                        $this->options[] = [
                            'title' => $brand->title,
                            'selected' => isset($argumentMap[$brand->id]) || $brand->requested,
                            'id' => $brand->id,
                            'url' => $brand->URL,
                        ];
                    }
                }
            }
        }
        return $this->options;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterBrandIds());
    }
//    protected function limitOptions(array $productsIds)
//    {
//        if ($productsIds) {
//            $this->options = $this->productsIdsToBrandsIds($productsIds);
//        }
//    }
//
//    protected function productsIdsToBrandsIds(array $productsIds)
//    {
//        $brandsIds = [];
//        $collection = persistableCollection::getInstance('structure_links');
//        $conditions = [
//            [
//                'childStructureId',
//                'IN',
//                $productsIds,
//            ],
//            [
//                'type',
//                '=',
//                "productbrand",
//            ],
//        ];
//        if ($records = $collection->conditionalLoad('distinct(parentStructureId)', $conditions, [], [], [], true)
//        ) {
//            foreach ($records as &$record) {
//                $brandsIds[] = $record['parentStructureId'];
//            }
//        }
//        return $brandsIds;
//    }
//
//    protected function loadRelatedIds()
//    {
//        $this->relatedIds = [];
//        if ($this->arguments) {
//            $collection = persistableCollection::getInstance('structure_links');
//            $conditions = [
//                [
//                    'parentStructureId',
//                    'IN',
//                    $this->arguments,
//                ],
//                [
//                    'type',
//                    '=',
//                    "productbrand",
//                ],
//            ];
//            if ($records = $collection->conditionalLoad('childStructureId', $conditions)) {
//                foreach ($records as &$record) {
//                    $this->relatedIds[] = $record['childStructureId'];
//                }
//            }
//        }
//    }
}