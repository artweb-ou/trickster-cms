<?php

class queryFiltersManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new queryFiltersManager();
    }

    public function makeInjections($instance)
    {
    }
}

