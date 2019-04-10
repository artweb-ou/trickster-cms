<?php

class apiQueriesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new apiQueriesManager();
    }

    public function makeInjections($instance)
    {
    }
}