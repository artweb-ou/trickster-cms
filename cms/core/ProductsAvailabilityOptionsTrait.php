<?php

trait ProductsAvailabilityOptionsTrait
{
    public $productsAvailabilityTypes = [
        'available',
        'quantity_dependent',
        'available_inquirable', // it is inquirable and buyable
        'inquirable', // it is inquirable only
        'unavailable',
    ];

    public function productsAvailabilityOptions($prefix = '', $start = 0)
    {
        $options = [];
        if ($start > 0 || !empty($prefix)){
            foreach ($this->productsAvailabilityTypes as $typeKey => $typeValue) {
                $options[$typeKey + $start] = $prefix . $typeValue; // start from 1 if need
            }
        }
        return $options;
    }

}