<?php

class ServerSessionManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ServerSessionManager();
    }

    /**
     * @param ServerSessionManager $instance
     * @return ServerSessionManager
     */
    public function makeInjections($instance)
    {
        if ($sessionName = $this->getOption('sessionName')) {
            $instance->setSessionName($sessionName);
        }

        $defaultLifeTime = $this->registry->getService('ConfigManager')->get('main.defaultSessionLifeTime');
        if ($defaultLifeTime) {
            $instance->setSessionLifeTime($defaultLifeTime);
        }
        $this->injectService($instance, 'PathsManager');
        return $instance;
    }
}