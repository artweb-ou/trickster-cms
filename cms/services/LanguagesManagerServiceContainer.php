<?php

class LanguagesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new LanguagesManager();
    }

    /**
     * @param LanguagesManager $instance
     * @return LanguagesManager
     */
    public function makeInjections($instance)
    {
        $this->injectService($instance, 'ServerSessionManager');
        return $instance;
    }
}

