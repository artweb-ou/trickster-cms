<?php

class ProductIconsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new ProductIconsManager();
    }

    public function makeInjections($instance)
    {
        $productIconsManager = $instance;
        if ($iconsManager = $this->registry->getService('IconsManager')) {
            $productIconsManager->setIconsManager($iconsManager);
        }
        if ($linksManager = $this->registry->getService('linksManager')) {
            $productIconsManager->setLinksManager($linksManager);
        }
        if ($structureManager = $this->registry->getService('structureManager')) {
            $productIconsManager->setStructureManager($structureManager);
        }
        if ($db = $this->registry->getService('db')) {
            $productIconsManager->setDb($db);
        }
        return $productIconsManager;
    }
}