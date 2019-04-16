<?php

class designThemesManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new designThemesManager();
    }

    /**
     * @param designThemesManager $instance
     * @return mixed
     */
    public function makeInjections($instance)
    {
        $configManager = $this->registry->getService('ConfigManager');
        if ($themesDirectoryPaths = $this->getOption('themesDirectoryPaths')) {
            foreach ($themesDirectoryPaths as &$path) {
                $instance->setThemesDirectoryPath($path);
            }
        } else {
            $controller = $this->registry->getService('controller');
            $themes = $configManager->get('paths.themes');
            foreach ($controller->getIncludePaths() as $path) {
                $instance->setThemesDirectoryPath($path . $themes);
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
        $instance->setCurrentThemeCode($currentThemeCode);
        return $instance;
    }
}