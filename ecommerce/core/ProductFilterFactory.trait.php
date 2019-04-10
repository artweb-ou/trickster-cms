<?php

trait ProductFilterFactoryTrait
{
    public function createProductFilter($type, $arguments = [], $initialOptions = [])
    {
        $pathsManager = $this->getService('PathsManager');
        $className = $type . 'ProductFilter';
        $modulesPath = $pathsManager->getRelativePath('modules');
        $filePath = $pathsManager->getIncludeFilePath($modulesPath . 'productFilters/' . $className . '.class.php');
        if ($filePath !== false) {
            include_once $filePath;
        }
        $filter = new $className($arguments, $initialOptions);
        if ($filter instanceof DependencyInjectionContextInterface) {
            $this->instantiateContext($filter);
        }
        return $filter;
    }
}