<?php

/**
 * Class ShoppingBasketCity
 */

class ShoppingBasketCity
{
    public $id = null;
    public $title = null;

    public function __construct($cityData)
    {
        $this->id = $cityData['id'];
        $this->title = $cityData['title'];
    }
}