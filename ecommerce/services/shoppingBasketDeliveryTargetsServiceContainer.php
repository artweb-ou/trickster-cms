<?php

class shoppingBasketDeliveryTargetsServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new shoppingBasketDeliveryTargets();
    }

    public function makeInjections($instance)
    {
    }
}

