<?php

class ResourcesUniterHelperServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ResourcesUniterHelper();
    }

    public function makeInjections($instance)
    {
        $resourcesUniterHelper = $instance;
        if ($currentThemeCode = $this->getOption('currentThemeCode')) {
            $resourcesUniterHelper->setCurrentThemeCode($currentThemeCode);
        }

        $designThemesManager = $this->registry->getService('DesignThemesManager');
        $resourcesUniterHelper->setDesignThemesManager($designThemesManager);

        $requestHeadersManager = $this->registry->getService('requestHeadersManager');
        if ($userAgentEngineType = $this->getOption('userAgentEngineType')) {
            $resourcesUniterHelper->setUserAgentEngineType($userAgentEngineType);
        } elseif ($userAgentEngineType = $requestHeadersManager->getUserAgentEngineType()) {
            $resourcesUniterHelper->setUserAgentEngineType($userAgentEngineType);
        }
        if ($userAgent = $this->getOption('userAgent')) {
            $resourcesUniterHelper->setUserAgent($userAgent);
        } elseif ($userAgent = $requestHeadersManager->getUserAgent()) {
            $resourcesUniterHelper->setUserAgent($userAgent);
        }
        if ($userAgentVersion = $this->getOption('userAgentVersion')) {
            $resourcesUniterHelper->setUserAgentVersion($userAgentVersion);
        } elseif ($userAgentVersion = $requestHeadersManager->getUserAgentVersion()) {
            $resourcesUniterHelper->setUserAgentVersion($userAgentVersion);
        }
        $pathsManager = $this->registry->getService('PathsManager');
        if ($option = $this->getOption('cssCachePath')) {
            $resourcesUniterHelper->setCssCachePath($option);
        } else {
            $resourcesUniterHelper->setCssCachePath($pathsManager->getPath('cssCache'));
        }
        if ($option = $this->getOption('jsCachePath')) {
            $resourcesUniterHelper->setJsCachePath($option);
        } else {
            $resourcesUniterHelper->setJsCachePath($pathsManager->getPath('javascriptCache'));
        }
        return $resourcesUniterHelper;
    }
}