<?php

use DI\Container;

/**
 * Trait DependencyInjectionContextTrait
 */
trait DependencyInjectionContextTrait
{
    private array $localServices = [];
    private Container $container;

    /**
     * Returns the service: checks local services first, then falls through to PHP-DI container.
     *
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    public function getService($type)
    {
        if (isset($this->localServices[$type])) {
            return $this->localServices[$type];
        }
        return $this->container->get($type);
    }

    protected function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * Stores a service in the local services registry.
     * Use this to override PHP-DI resolution for context-specific instances
     * (e.g. the structureManager instance appropriate for this context).
     */
    public function setService($type, $object): void
    {
        $this->localServices[$type] = $object;
    }

    /**
     * Replaces the entire local services map.
     * Called by instantiateContext to propagate parent's services to child objects.
     */
    public function setLocalServices(array $services): void
    {
        $this->localServices = $services;
    }

    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * Passes current DI context (local services + container) to a child object,
     * so the child can resolve services from the same context.
     */
    protected function instantiateContext(DependencyInjectionContextInterface $object): void
    {
        $object->setLocalServices($this->localServices);
        $object->setContainer($this->container);
    }
}

