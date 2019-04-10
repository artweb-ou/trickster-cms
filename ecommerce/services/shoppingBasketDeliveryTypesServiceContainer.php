<?php

class shoppingBasketDeliveryTypesServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new shoppingBasketDeliveryTypes();
    }

    public function makeInjections($instance)
    {
    }
}