<?php

trait productsAvailabilityOptionsTrait
{
    public $productsAvailabilityValues = [
        'available',
        'quantity_dependent',
        'available_inquirable', // it is inquirable and buyable
        'inquirable', // it is inquirable only
        'unavailable',
//        'instock',
    ];

    public function productsAvailOptions($prefix='', $start=0){
        $options = [];
        foreach ($this->productsAvailabilityValues as $valueKey=>$valueValue) {
            $options[$valueKey + $start] =  $prefix. $valueValue; // start from 1 if need
        }
        return $options;
    }

}