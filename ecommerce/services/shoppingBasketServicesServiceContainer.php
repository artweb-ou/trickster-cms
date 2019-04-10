<?php

class shoppingBasketServicesServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new shoppingBasketServices();
    }

    public function makeInjections($instance)
    {
    }
}

