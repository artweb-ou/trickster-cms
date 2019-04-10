<?php

class linksManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new linksManager();
    }

    public function makeInjections($instance)
    {
    }
}