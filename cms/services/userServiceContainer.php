<?php

class userServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new user();
    }

    public function makeInjections($instance)
    {
        $user = $instance;
        $service = $this->getOption('privilegesManager')
            ?: $this->registry->getService('privilegesManager');
        $user->setPrivilegesManager($service);
        $service = $this->getOption('serverSessionManager')
            ?: $this->registry->getService('serverSessionManager');
        $user->setServerSessionManager($service);
        $service = $this->getOption('db')
            ?: $this->registry->getService('db');
        $user->setDb($service);
        $user->initialize();

        return $user;
    }
}
