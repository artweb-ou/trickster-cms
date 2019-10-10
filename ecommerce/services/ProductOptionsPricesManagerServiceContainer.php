<?php

class ProductOptionsPricesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ProductOptionsPricesManager();
    }

    /**
     * @param ProductOptionsPricesManager $instance
     * @return mixed
     */
    public function makeInjections($instance)
    {
        $this->injectService($instance, 'db');
        $this->injectService($instance, 'ParametersManager');
        $instance->setDb($this->registry->getService('db'));
        return $instance;
    }
}