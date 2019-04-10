<?php

class ParametersManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ParametersManager();
    }

    public function makeInjections($instance)
    {
        $parametersManager = $instance;
        $parametersManager->setDb($this->registry->getService('db'));
        $parametersManager->setLanguagesManager($this->registry->getService('languagesManager'));
        return $parametersManager;
    }
}