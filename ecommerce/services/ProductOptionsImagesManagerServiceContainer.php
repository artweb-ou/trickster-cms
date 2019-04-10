<?php

class ProductOptionsImagesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ProductOptionsImagesManager();
    }

    public function makeInjections($instance)
    {
        $service = $instance;
        $service->setDb($this->registry->getService('db'));
        return $service;
    }
}