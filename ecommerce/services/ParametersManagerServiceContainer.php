<?php

class ParametersManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ParametersManager();
    }

    /**
     * @param ParametersManager $instance
     * @return mixed
     */
    public function makeInjections($instance)
    {
        $instance->setDb($this->registry->getService('db'));
        $instance->setLanguagesManager($this->registry->getService('languagesManager'));
        return $instance;
    }
}