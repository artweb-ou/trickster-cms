<?php

use App\Paths\PathsManager;

class UpdatesApiServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new UpdatesApi();
    }

    public function makeInjections($instance)
    {
        $result = $instance;
        $configManager = $this->registry->getService('ConfigManager');
        $pathsManager = $this->registry->getService(PathsManager::class);
        $result->setApiUrl($configManager->get('main.updatesUrl'));
        $result->setLicenceKey($configManager->get('main.licenceKey'));
        $result->setLicenceName($configManager->get('main.licenceName'));
        $path = $pathsManager->getPath('newDeployments');
        $pathsManager->ensureDirectory($path);
        $result->setWorkspaceDir($path);
        return $result;
    }
}

