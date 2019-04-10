<?php

class brandProductFilter extends productFilter
{
    protected $type = 'brand';

    public function getOptionsInfo()
    {
        $info = [];
        if ($this->options) {
            $argumentMap = array_flip($this->arguments);
            $structureManager = $this->getService('structureManager');
            $brands = $structureManager->getElementsByIdList($this->options);
            $titles = [];
            foreach ($brands as &$brand) {
                $info[] = [
                    'title' => $brand->title,
                    'selected' => isset($argumentMap[$brand->id]),
                    'id' => $brand->id,
                ];
                $titles[] = mb_strtolower($brand->title);
            }
            array_multisort($titles, $info);
        }
        return $info;
    }

    protected function limitOptions(array $productsIds)
    {
        if ($productsIds) {
            $this->options = $this->productsIdsToBrandsIds($productsIds);
        }
    }

    protected function productsIdsToBrandsIds(array $productsIds)
    {
        $brandsIds = [];
        $collection = persistableCollection::getInstance('structure_links');
        $conditions = [
            [
                'childStructureId',
                'IN',
                $productsIds,
            ],
            [
                'type',
                '=',
                "productbrand",
            ],
        ];
        if ($records = $collection->conditionalLoad('distinct(parentStructureId)', $conditions, [], [], [], true)
        ) {
            foreach ($records as &$record) {
                $brandsIds[] = $record['parentStructureId'];
            }
        }
        return $brandsIds;
    }

    protected function loadRelatedIds()
    {
        $this->relatedIds = [];
        if ($this->arguments) {
            $collection = persistableCollection::getInstance('structure_links');
            $conditions = [
                [
                    'parentStructureId',
                    'IN',
                    $this->arguments,
                ],
                [
                    'type',
                    '=',
                    "productbrand",
                ],
            ];
            if ($records = $collection->conditionalLoad('childStructureId', $conditions)) {
                foreach ($records as &$record) {
                    $this->relatedIds[] = $record['childStructureId'];
                }
            }
        }
    }
}