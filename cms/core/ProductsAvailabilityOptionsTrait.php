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

    public function productsAvailabilityOptionsList()
    {
        return $this->productsAvailabilityTypes;
    }

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


    /**
     * @return array
     */
    public function getProductsAvailabilityOptions()
    {
        //  return $this->productsAvailabilityTypes;
        return $this->productsAvailabilityOptions('',1);
    }


}