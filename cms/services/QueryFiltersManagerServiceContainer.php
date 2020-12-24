<?php

class QueryFiltersManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new QueryFiltersManager();
    }

    public function makeInjections($instance)
    {
    }
}

