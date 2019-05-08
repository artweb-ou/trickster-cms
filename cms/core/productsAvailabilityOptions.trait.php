<?php

trait productsAvailabilityOptionsTrait
{
    public $productsAvailabilityValues = [
        'available',
        'quantity_dependent',
        'inquirable',
        'unavailable',
        'available_inquirable',
        'inquirable_only',
        'inquirable_and_buyable',
    ];

    public function productsAvailOptions($prefix='', $start=0){
        $options = [];
        foreach ($this->productsAvailabilityValues as $valueKey=>$valueValue) {
            $options[$valueKey + $start] =  $prefix. $valueValue; // start from 1 if need
        }
        return $options;
    }

}