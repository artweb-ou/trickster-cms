<?php

class DirectoSyncServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new DirectoSync();
    }

    public function makeInjections($instance)
    {
        $directoSync = $instance;
        $directoSync->setDb($this->registry->getService('db'));
        $directoSync->setLinksManager($this->registry->getService('linksManager'));
        $directoSync->setStructureManager($this->registry->getService('structureManager'));
        $directoSync->setDirecto($this->registry->getService('Directo'));
        return $directoSync;
    }
}