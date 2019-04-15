<?php

class ApiQueriesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ApiQueriesManager();
    }

    public function makeInjections($instance)
    {
    }
}