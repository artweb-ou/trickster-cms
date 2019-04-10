<?php

class requestHeadersManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new requestHeadersManager();
    }

    public function makeInjections($instance)
    {
    }
}

