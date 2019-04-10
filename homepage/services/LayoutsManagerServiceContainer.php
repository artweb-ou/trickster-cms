<?php

class LayoutsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new LayoutsManager();
    }

    public function makeInjections($instance)
    {
        $layoutsManager = $instance;
        $structureManager = $this->registry->getService('structureManager');
        $layoutsManager->setStructureManager($structureManager);
        return $layoutsManager;
    }
}