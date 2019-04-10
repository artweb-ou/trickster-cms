<?php

class autoLoadManager
{
    public function __construct()
    {
        spl_autoload_register('autoLoadManager::handler', true, true);
    }

    public static function handler($className)
    {
        if ($controller = controller::getInstance()) {
            $pathsManager = $controller->getPathsManager();
            $corePath = $pathsManager->getRelativePath('core');
            if ($filePath = $pathsManager->getIncludeFilePath($corePath . $className . '.php')) {
                include_once($filePath);
            } elseif ($filePath = $pathsManager->getIncludeFilePath($corePath . $className . '.class.php')) {
                include_once($filePath);
            } elseif ($className == 'transportObject') {
                if ($filePath = $pathsManager->getIncludeFilePath($corePath . 'transportObject.interface.php')) {
                    include_once($filePath);
                }
            } elseif (strpos($className, 'Interface') !== false) {
                if ($filePath = $pathsManager->getIncludeFilePath($corePath . str_replace('Interface', '', $className) . '.interface.php')
                ) {
                    include_once($filePath);
                }
            } elseif (strpos($className, 'Trait') !== false) {
                if ($filePath = $pathsManager->getIncludeFilePath(
                    $corePath . str_replace('Trait', '', $className) . '.trait.php'
                )
                ) {
                    include_once($filePath);
                }
            } elseif (strpos($className, 'ProductFilter') !== false) {
                $filePath = $pathsManager->getIncludeFilePath($pathsManager->getRelativePath('modules')
                    . 'productFilters/' . $className . '.class.php');
                if ($filePath !== false) {
                    include_once $filePath;
                }
            } elseif (substr($className, -7) === 'Element') {
                $type = substr($className, 0, -7);
                $fileDirectory = $pathsManager->getRelativePath('structureElements') . $type . '/';
                if ($filePath = $pathsManager->getIncludeFilePath($fileDirectory . 'structure.class.php')) {
                    include_once($filePath);
                }
            } elseif (substr($className, -9) === 'DataChunk') {
                if (!class_exists($className, false)) {
                    $type = substr($className, 0, -9);
                    $fileDirectory = $pathsManager->getRelativePath('dataChunks');
                    if ($filePath = $pathsManager->getIncludeFilePath($fileDirectory . $type . '.class.php')) {
                        include_once($filePath);
                    }
                }
            }
        }
    }
}
