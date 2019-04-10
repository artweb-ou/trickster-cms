<?php

class VisitorsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new VisitorsManager();
    }

    public function makeInjections($instance)
    {
        $this->injectService($instance, 'statsDb');
        $this->injectService($instance, 'user');
        $this->injectService($instance, 'eventsLog');
    }
}