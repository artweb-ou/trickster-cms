<?php

use DI\Container;

class DependencyInjectionServicesRegistry implements DependencyInjectionServicesRegistryInterface
{
    protected $services = [];

    public function __construct(
        private Container $container,
        protected array   $paths = [],
    )
    {
    }

    public function setService($type, $object)
    {
        $this->services[$type] = $object;
    }

    public function getService($type, $options = null, $forceNew = false, $updateRegistry = true)
    {
        if (!isset($this->services[$type]) || $forceNew) {
            $service = $this->createService($type, $options, $updateRegistry);
        } else {
            $service = $this->services[$type];
        }
        return $service;
    }

    protected function createService($type, $options, $updateRegistry)
    {
        $service = false;
        $className = $type . 'ServiceContainer';
        if (!class_exists($className, false)) {
            foreach ($this->paths as $path) {
                $filePath = $path . $className . '.php';
                $filePathLegacy = $path . lcfirst($className) . '.php';
                if (is_file($filePath)) {
                    include_once($filePath);
                    break;
                }
                if (is_file($filePathLegacy)) {
                    include_once($filePathLegacy);
                    break;
                }
            }
        }
        if (class_exists($className, false)) {
            $serviceContainer = new $className();
            if ($serviceContainer instanceof DependencyInjectionServiceContainerInterface) {
                if ($options) {
                    $serviceContainer->setOptions($options);
                }
                $serviceContainer->setRegistry($this);

                $container = controller::getInstance()->getContainer();

                if ($service = $serviceContainer->makeInstance()) {
                    if ($updateRegistry) {
                        $this->services[$type] = $service;
                    }
                    $serviceContainer->makeInjections($service);

                    //If we create a service, which implements DI context passing interface,
                    //then we should pass current registry to this service, so service could
                    //use it in it's functionality
                    if ($service instanceof DependencyInjectionContextInterface) {
                        $service->setRegistry($this);
                        $service->setContainer($container);
                    }
                }
            }
        }
        if (!$service){
            $service = $this->container->get($type) ?? false;
        }
        return $service;
    }
}

interface DependencyInjectionServicesRegistryInterface
{
    public function getService($type, $options = null, $forceNew = false);
}

interface DependencyInjectionServiceContainerInterface
{
    public function setOptions($options);

    public function setRegistry(DependencyInjectionServicesRegistryInterface $registry);

    public function makeInstance();

    public function makeInjections($instance);
}

abstract class DependencyInjectionServiceContainer implements DependencyInjectionServiceContainerInterface
{
    protected $options;
    protected DependencyInjectionServicesRegistryInterface $registry;

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function setRegistry(DependencyInjectionServicesRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getOption($name): mixed
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
        return false;
    }

    abstract public function makeInstance();

    abstract public function makeInjections($instance);

    protected function injectService($instance, $serviceName)
    {
        $setterName = 'set' . ucfirst($serviceName);
        if ($service = $this->getOption($serviceName)) {
            $instance->$setterName($service);
        } else {
            $instance->$setterName($this->registry->getService($serviceName));
        }
    }
}