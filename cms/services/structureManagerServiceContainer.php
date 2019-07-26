<?php

class structureManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new structureManager();
    }

    /**
     * @param structureManager $instance
     * @return mixed
     */
    public function makeInjections($instance)
    {
        $structureManager = $instance;
        /**
         * @var ConfigManager $configManager
         */
        $configManager = $this->registry->getService('ConfigManager');
        if ($requestedPath = $this->getOption('requestedPath')) {
            $structureManager->setRequestedPath($requestedPath);
        } elseif ($controller = $this->registry->getService('controller')) {
            $structureManager->setRequestedPath($controller->requestedPath);
        }

        if ($linksManager = $this->getOption('linksManager')) {
            $structureManager->setLinksManager($linksManager);
        } else {
            $structureManager->setLinksManager($this->registry->getService('linksManager'));
        }

        if ($languagesManager = $this->getOption('LanguagesManager')) {
            $structureManager->setLanguagesManager($languagesManager);
        } else {
            $languagesManager = $this->registry->getService('LanguagesManager');
            $structureManager->setLanguagesManager($languagesManager);
        }

        if ($privilegesManager = $this->getOption('privilegesManager')) {
            $structureManager->setPrivilegesManager($privilegesManager);
        } else {
            $structureManager->setPrivilegesManager($this->registry->getService('privilegesManager'));
        }

        if ($rootUrl = $this->getOption('rootUrl')) {
            $structureManager->setRootUrl($rootUrl);
        } else {
            $controller = $this->registry->getService('controller');
            $structureManager->setRootUrl($controller->rootURL);
        }

        $adminRootMarker = $configManager->get('main.rootMarkerAdmin');
        if (!($rootMarker = $this->getOption('rootMarker'))) {
            $rootMarker = $adminRootMarker;
        }
        $structureManager->setRootElementMarker($rootMarker);

        if ($rootMarker == $adminRootMarker) {
            $structureManager->setPathSearchAllowedLinks($configManager->getMerged('structurelinks.adminAllowed'));
        } else {
            $structureManager->setPathSearchAllowedLinks($configManager->getMerged('structurelinks.publicAllowed'));
            //we only need to restrict globally path search by current language if we have cache enabled.
            //otherwise this makes a huge performance hit which has to be investigated (todo: investigate)
            if ($cache = $this->registry->getService('Cache')){
                if ($cache->isEnabled()){
                    $structureManager->setElementPathRestrictionId($languagesManager->getCurrentLanguageId());
                }
            }
        }


        if ($rootId = $this->getOption('rootId')) {
            $structureManager->setRootElementId($rootId);
        }
        $this->injectService($instance, 'Cache');

        if ($configActions = $this->getOption('configActions')) {
            $structureManager->defaultActions = $configManager->getConfig('actions')->getLinkedData();
        }

        $deniedCopyLinkTypes = [];
        if ($config = $configManager->getConfig('deniedCopyLinkTypes')) {
            $data = $config->getLinkedData();
            $deniedCopyLinkTypes = array_keys(array_filter($data));
        }
        if ($deniedCopyLinkTypes) {
            $structureManager->setDeniedCopyLinkTypes($deniedCopyLinkTypes);
        }

        return $structureManager;
    }
}