<?php

class settingsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new settingsManager();
    }

    public function makeInjections($instance)
    {
    }
}