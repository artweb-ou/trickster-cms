<?php

trait ProductFilterFactoryTrait
{
    public function createProductFilter($type, $initialOptions = null)
    {
        $className = ucfirst($type) . 'ProductFilter';
        /**
         * @var productFilter $filter
         */
        $filter = new $className($this, $initialOptions);
        if ($filter instanceof DependencyInjectionContextInterface) {
            $this->instantiateContext($filter);
        }
        return $filter;
    }
}