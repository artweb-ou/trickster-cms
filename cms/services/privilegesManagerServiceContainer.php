<?php

class privilegesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new privilegesManager();
    }

    public function makeInjections($instance)
    {
    }
}

