<?php

class designThemesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new designThemesManager();
    }

    public function makeInjections($instance)
    {
        $configManager = $this->registry->getService('ConfigManager');
        $designThemesManager = $instance;
        if ($themesDirectoryPaths = $this->getOption('themesDirectoryPaths')) {
            foreach ($themesDirectoryPaths as &$path) {
                $designThemesManager->setThemesDirectoryPath($path);
            }
        } else {
            $controller = $this->registry->getService('controller');
            $themes = $configManager->get('paths.themes');
            foreach ($controller->getIncludePaths() as $path) {
                $designThemesManager->setThemesDirectoryPath($path . $themes);
            }
        }
        if (!($currentThemeCode = $this->getOption('currentThemeCode'))) {
            if ($controllerApplication = $this->registry->getService('controllerApplication')) {
                if ($controllerApplication instanceof ThemeCodeProviderInterface) {
                    $currentThemeCode = $controllerApplication->getThemeCode();
                }
            }
            if (!$currentThemeCode) {
                $currentThemeCode = $configManager->get('main.publicTheme');
            }
        }
        $designThemesManager->setCurrentThemeCode($currentThemeCode);
        return $designThemesManager;
    }
}