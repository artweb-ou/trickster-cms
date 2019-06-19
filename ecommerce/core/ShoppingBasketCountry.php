<?php

/**
 * Class ShoppingBasketCountry
 */
class ShoppingBasketCountry
{
    public $id = null;
    public $title = null;
    public $iso3166_1a2 = null;
    public $conditionsText = null;
    public $citiesList = [];
    public $citiesIndex = [];

    public function __construct($countryData)
    {
        $this->id = $countryData['id'];
        $this->title = $countryData['title'];
        $this->iso3166_1a2 = $countryData['iso3166_1a2'];
        $this->conditionsText = $countryData['conditionsText'];
        foreach ($countryData['cities'] as &$cityData) {
            $city = new shoppingBasketCity($cityData);
            $this->citiesList[] = $city;
            $this->citiesIndex[$city->id] = $city;
        }
    }

    /* TODO: rename this method */
    public function getActiveCitiesList()
    {
        return $this->citiesList;
    }
}
