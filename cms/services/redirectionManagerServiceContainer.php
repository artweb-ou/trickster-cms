<?php

class redirectionManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new redirectionManager();
    }

    public function makeInjections($instance)
    {
    }
}

