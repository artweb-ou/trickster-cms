<?php

class IconsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new IconsManager();
    }

    public function makeInjections($instance)
    {
        $iconsManager = $instance;
        if ($structureManager = $this->registry->getService('structureManager')) {
            $iconsManager->setStructureManager($structureManager);
        }

        if ($db = $this->registry->getService('db')) {
            $iconsManager->setDb($db);
        }

        return $iconsManager;
    }
}