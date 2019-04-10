<?php

class uriSwitchLogicsServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new uriSwitchLogics();
    }

    public function makeInjections($instance)
    {
    }
}