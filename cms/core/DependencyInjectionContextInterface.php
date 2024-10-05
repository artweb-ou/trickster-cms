<?php

use DI\Container;

interface DependencyInjectionContextInterface
{
    public function setRegistry(DependencyInjectionServicesRegistryInterface $registry);

    public function setContainer(Container $container);
}