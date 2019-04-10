<?php

class DirectoServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new Directo();
    }

    public function makeInjections($instance)
    {
    }
}