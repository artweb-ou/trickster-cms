<?php

class bankLogServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new BankLog();
    }

    public function makeInjections($instance)
    {
    }
}