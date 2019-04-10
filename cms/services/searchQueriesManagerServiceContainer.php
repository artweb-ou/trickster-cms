<?php

class searchQueriesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new searchQueriesManager();
    }

    public function makeInjections($instance)
    {
    }
}

