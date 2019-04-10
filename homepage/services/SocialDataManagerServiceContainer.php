<?php

class SocialDataManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new SocialDataManager();
    }

    public function makeInjections($instance)
    {
    }
}