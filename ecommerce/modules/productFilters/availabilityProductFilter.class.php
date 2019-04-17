<?php

class availabilityProductFilter extends productFilter
{
    protected $type = 'availability';
    protected $relevantIds = [];
    protected $availableOnly = false;
    protected $availableIds;
    public $titleEnabled = false;
    protected $relevant = false;

    public function __construct(ProductsListStructureElement $element)
    {
        parent::__construct($element);
        $this->options = [
            'all' => true,
            'available' => true,
        ];
//        if (count($arguments) === 1) {
//            $this->availableOnly = $arguments[0];
//        }
    }

    public function getOptionsInfo()
    {
        $info = [];
        $translationsManager = $this->getService('translationsManager');
        $argumentMap = array_flip($this->arguments);
        if (isset($this->options['all'])) {
            $info[] = [
                'title' => $translationsManager->getTranslationByName('product_filter.availability_all'),
                'selected' => isset($argumentMap[0]),
                'id' => '',
            ];
        }
        if (isset($this->options['available'])) {
            $info[] = [
                'title' => $translationsManager->getTranslationByName('product_filter.availability_available'),
                'selected' => isset($argumentMap[1]),
                'id' => 1,
            ];
        }
        return $info;
    }

    protected function getArguments()
    {
        return true;
    }
//    protected function limitOptions(array $productsIds)
//    {
//        if (!$productsIds || !array_intersect($productsIds, $this->getAvailableIds())) {
//            unset($this->options['available']);
//        }
//    }
//
//    public function filter(array &$ids = [])
//    {
//        if ($this->availableOnly) {
//            $ids = array_intersect($ids, $this->getAvailableIds());
//        }
//    }
//
//    protected function loadRelatedIds()
//    {
//    }
//
//    protected function getAvailableIds()
//    {
//        if ($this->availableIds === null) {
//            $this->availableIds = [];
//            $collection = persistableCollection::getInstance('module_product');
//            $orConditions = [
//                [
//                    [
//                        'availability',
//                        '=',
//                        'available',
//                    ],
//                    [
//                        'inactive',
//                        '!=',
//                        '1',
//                    ],
//                ],
//                [
//                    [
//                        'availability',
//                        '=',
//                        'quantity_dependent',
//                    ],
//                    [
//                        'quantity',
//                        '!=',
//                        '0',
//                    ],
//                    [
//                        'inactive',
//                        '!=',
//                        '1',
//                    ],
//                ],
//            ];
//            if ($records = $collection->conditionalOrLoad('distinct(id)', $orConditions, [], [], [], true)
//            ) {
//                foreach ($records as &$record) {
//                    $this->availableIds[] = $record['id'];
//                }
//            }
//        }
//        return $this->availableIds;
//    }
}