<?php

class ShoppingBasketDeliveryTargetsServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ShoppingBasketDeliveryTargets();
    }

    public function makeInjections($instance)
    {
    }
}

