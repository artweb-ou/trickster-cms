<?php

class ApiQueryResultResolverServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ApiQueryResultResolver();
    }

    public function makeInjections($instance)
    {
    }
}