<?php

/**
 * Trait DependencyInjectionContextTrait
 */
trait DependencyInjectionContextTrait
{
    /**
     * @var DependencyInjectionServicesRegistry
     */
    private $registry;

    /**
     * Returns the service from attached registry
     *
     * @param string $type
     * @param array $options
     * @param bool $forceNew
     * @param bool $updateRegistry
     * @return bool|mixed
     */
    public function getService($type, $options = null, $forceNew = false, $updateRegistry = true)
    {
        if ($registry = $this->getRegistry()) {
            if ($service = $registry->getService($type, $options, $forceNew, $updateRegistry)) {
                return $service;
            }
        }
        return false;
    }

    /**
     * Define whether we use normal registry or a failsafe global solution for deprecated classes
     *
     * @return bool|DependencyInjectionServicesRegistry
     */
    protected function getRegistry()
    {
        if ($this->registry) {
            return $this->registry;
        } elseif ($GLOBALS['dependencyInjectionContextGlobalRegistry']) {
            return $GLOBALS['dependencyInjectionContextGlobalRegistry'];
        }
        return false;
    }

    /**
     * Sets the externally created service
     *
     * @param string $type
     * @param $object
     * @return bool
     */
    protected function setService($type, $object)
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
     * @param DependencyInjectionServicesRegistryInterface $registry
     */
    public function setRegistry(DependencyInjectionServicesRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Global registry should not be used with new classes. It's a fallback for old singleton classes, which are
     * not instantiated properly by some temporary reason.
     *
     * @param DependencyInjectionServicesRegistryInterface $registry
     */
    public static function setGlobalRegistry(DependencyInjectionServicesRegistryInterface $registry)
    {
        $GLOBALS['dependencyInjectionContextGlobalRegistry'] = $registry;
    }

    /**
     * If we create a service, which implements DI context passing interface, then we should pass current
     * registry to this service, so service could use it in it's functionality
     *
     * @param DependencyInjectionContextInterface $object
     */
    protected function instantiateContext(DependencyInjectionContextInterface $object)
    {
        if ($registry = $this->getRegistry()) {
            $object->setRegistry($registry);
        }
    }
}

