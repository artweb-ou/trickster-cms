<?php

use App\Structure\ActionFactory;

class ActionFactoryServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ActionFactory();
    }

    public function makeInjections($instance)
    {
    }
}