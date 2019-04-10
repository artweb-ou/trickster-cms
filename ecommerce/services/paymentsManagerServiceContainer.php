<?php

class paymentsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new paymentsManager();
    }

    public function makeInjections($instance)
    {
    }
}