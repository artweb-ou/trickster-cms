<?php

class redirectionManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new RedirectionManager();
    }

    public function makeInjections($instance)
    {
    }
}

