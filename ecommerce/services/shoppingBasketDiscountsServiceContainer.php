<?php

class shoppingBasketDiscountsServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new shoppingBasketDiscounts();
    }

    public function makeInjections($instance)
    {
        $shoppingBasketDiscounts = $instance;
        //pass registry manually until shoppingBasketDiscounts is refactored to avoid initialize method
        $shoppingBasketDiscounts->setRegistry($this->registry);
        $shoppingBasketDiscounts->initialize();
        return $shoppingBasketDiscounts;
    }
}

