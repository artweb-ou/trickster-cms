<?php

use DI\Container;

interface DependencyInjectionContextInterface
{
    public function setLocalServices(array $services);

    public function setContainer(Container $container);
}