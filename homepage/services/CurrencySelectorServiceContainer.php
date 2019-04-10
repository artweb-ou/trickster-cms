<?php

class CurrencySelectorServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new CurrencySelector();
    }

    public function makeInjections($instance)
    {
    }
}