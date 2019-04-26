<?php

class AvailabilityProductFilter extends ProductFilter
{
    protected $type = 'availability';

    public function getOptionsInfo()
    {
        if ($this->optionsInfo === null) {
            $this->optionsInfo = [];
            if ($availabilityTypes = $this->productsListElement->getProductsListAvailabilityTypes()) {
                $availabilityTypes = array_flip($availabilityTypes);
                $translationsManager = $this->getService('translationsManager');

                $argumentMap = $this->getArguments();
                if (isset($availabilityTypes['available']) || isset($availabilityTypes['quantity_dependent']) || isset($availabilityTypes['available_inquirable'])) {
                    $this->optionsInfo[] = [
                        'title' => $translationsManager->getTranslationByName('product_filter.availability_available'),
                        'selected' => isset($argumentMap['available']),
                        'id' => 'available',
                    ];
                }
                if (isset($availabilityTypes['inquirable'])) {
                    $this->optionsInfo[] = [
                        'title' => $translationsManager->getTranslationByName('product_filter.availability_inquirable'),
                        'selected' => isset($argumentMap['inquirable']),
                        'id' => 'inquirable',
                    ];
                }
            }
        }
        return $this->optionsInfo;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterAvailability());
    }
}