<?php

class eventsLogServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new eventsLog();
    }

    public function makeInjections($instance)
    {
        $this->injectService($instance, 'db');
        $this->injectService($instance, 'statsDb');
        $this->injectService($instance, 'VisitorsManager');

        return $instance;
    }
}