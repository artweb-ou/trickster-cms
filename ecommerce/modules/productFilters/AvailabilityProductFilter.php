<?php

class AvailabilityProductFilter extends productFilter
{
    protected $type = 'availability';

    public function getOptionsInfo()
    {
        if ($this->options === null) {
            $this->options = [];
            if ($availabilityTypes = $this->productsListElement->getProductsListAvailabilityTypes()) {
                $availabilityTypes = array_flip($availabilityTypes);
                $translationsManager = $this->getService('translationsManager');

                $argumentMap = $this->getArguments();
//                $this->options[] = [
//                    'title' => $translationsManager->getTranslationByName('product_filter.availability_all'),
//                    'selected' => !$argumentMap,
//                    'id' => '',
//                ];

                if (isset($availabilityTypes['available']) || isset($availabilityTypes['quantity_dependent']) || isset($availabilityTypes['available_inquirable'])) {
                    $this->options[] = [
                        'title' => $translationsManager->getTranslationByName('product_filter.availability_available'),
                        'selected' => isset($argumentMap['available']),
                        'id' => 'available',
                    ];
                }
                if (isset($availabilityTypes['inquirable'])) {
                    $this->options[] = [
                        'title' => $translationsManager->getTranslationByName('product_filter.availability_inquirable'),
                        'selected' => isset($argumentMap['inquirable']),
                        'id' => 'inquirable',
                    ];
                }
            }
        }
        return $this->options;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterAvailability());
    }
}