<?php

class languagesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new languagesManager();
    }

    public function makeInjections($instance)
    {
    }
}

