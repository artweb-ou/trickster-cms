<?php

use DI\Container;

/**
 * Trait DependencyInjectionContextTrait
 */
trait DependencyInjectionContextTrait
{
    private DependencyInjectionServicesRegistryInterface $registry;
    private Container $container;

    /**
     * Returns the service from attached registry
     *
     * @template T
     * @param class-string<T> $type
     * @param array|null $options
     * @param bool $forceNew
     * @param bool $updateRegistry
     * @return T
     */
    public function getService($type, $options = null, $forceNew = false, $updateRegistry = true)
    {
        if ($registry = $this->getRegistry()) {
            if ($service = $registry->getService($type, $options, $forceNew, $updateRegistry)) {
                return $service;
            }
        }
        if (isset($this->container)) {
            return $this->container->get($type);
        }
        throw new RuntimeException('Service ' . $type . ' not found');
    }

    protected function getRegistry(): ?DependencyInjectionServicesRegistryInterface
    {
        return $this->registry;
    }

    protected function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * Sets the externally created service
     *
     * @param string $type
     */
    protected function setService($type, $object): bool
    {
        if ($this->registry) {
            $this->registry->setService($type, $object);
            return true;
        }
        return false;
    }

    /**
     * Set external registry which will be inherited by all other created classes
     *
     */
    public function setRegistry(DependencyInjectionServicesRegistryInterface $registry): void
    {
        $this->registry = $registry;
    }

    public function setContainer(Container $registry): void
    {
        $this->container = $registry;
    }

    /**
     * If we create a service, which implements DI context passing interface, then we should pass current
     * registry to this service, so service could use it in it's functionality
     *
     */
    protected function instantiateContext(DependencyInjectionContextInterface $object): void
    {
        if ($registry = $this->getRegistry()) {
            $object->setRegistry($registry);
        }
        $object->setContainer($this->container);
    }
}

