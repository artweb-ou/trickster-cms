<?php

class serverSessionManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new serverSessionManager();
    }

    public function makeInjections($instance)
    {
        $serverSessionManager = $instance;
        if ($sessionName = $this->getOption('sessionName')) {
            $serverSessionManager->setSessionName($sessionName);
        }

        $defaultLifeTime = $this->registry->getService('ConfigManager')->get('main.defaultSessionLifeTime');
        if ($defaultLifeTime) {
            $serverSessionManager->setSessionLifeTime($defaultLifeTime);
        }

        return $serverSessionManager;
    }
}