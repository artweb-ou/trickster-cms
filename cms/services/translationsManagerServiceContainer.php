<?php

class translationsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new translationsManager();
    }

    public function makeInjections($instance)
    {
    }
}