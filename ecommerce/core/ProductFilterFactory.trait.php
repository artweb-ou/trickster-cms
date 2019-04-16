<?php

trait ProductFilterFactoryTrait
{
    public function createProductFilter($type, $initialOptions = null)
    {
        /**
         * @var PathsManager $pathsManager
         */
        $pathsManager = $this->getService('PathsManager');
        $className = $type . 'ProductFilter';
        $modulesPath = $pathsManager->getRelativePath('modules');
        $filePath = $pathsManager->getIncludeFilePath($modulesPath . 'productFilters/' . $className . '.class.php');
        if ($filePath !== false) {
            include_once $filePath;
        }
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