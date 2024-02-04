<?php

class ActionsLogRepositoryServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ActionsLogRepository();
    }

    public function makeInjections($instance)
    {
        $this->injectService($instance, 'db');
        return $instance;
    }
}