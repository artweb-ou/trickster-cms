<?php

class breadcrumbsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new breadcrumbsManager();
    }

    /**
     * @param $instance breadcrumbsManager
     */
    public function makeInjections($instance)
    {
        if ($config = $this->getOption('config')) {
            $instance->setConfig($config);
        }
    }
}
