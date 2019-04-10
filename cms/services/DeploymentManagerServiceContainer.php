<?php

class DeploymentManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new DeploymentManager();
    }

    public function makeInjections($instance)
    {
        $deploymentManager = $instance;
        $deploymentManager->setDirectory(ROOT_PATH . '../deployments/');
        if (!($incomingDirectory = $this->getOption('incomingDirectory'))) {
            $pathsManager = $this->registry->getService('PathsManager');
            if ($path = $pathsManager->getPath('newDeployments')) {
                $incomingDirectory = $path;
            }
        }
        $deploymentManager->setIncomingDirectory($incomingDirectory);
        return $deploymentManager;
    }
}