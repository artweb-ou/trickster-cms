<?php

class CacheServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new Cache();
    }

    public function makeInjections($instance)
    {
    }
}