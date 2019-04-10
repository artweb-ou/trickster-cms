<?php

class ConfigManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return controller::getInstance()->getConfigManager();
    }

    public function makeInjections($instance)
    {
    }
}