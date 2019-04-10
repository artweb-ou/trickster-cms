<?php

class shoppingBasketServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new shoppingBasket();
    }

    public function makeInjections($instance)
    {
        $shoppingBasket = $instance;
        //pass registry manually until shoppingBasket is refactored to avoid initialize method
        $shoppingBasket->setRegistry($this->registry);
        $shoppingBasket->initialize();
        return $shoppingBasket;
    }
}

