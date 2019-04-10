<?php

class ProductOptionsPricesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ProductOptionsPricesManager();
    }

    public function makeInjections($instance)
    {
        $service = $instance;
        $service->setDb($this->registry->getService('db'));
        return $service;
    }
}