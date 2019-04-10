<?php

class WarehouseSyncronizerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        $warehouseSyncronizer = false;
        if ($warehouse = $this->getOption('warehouse')) {
            $warehouseSyncronizer = new WarehouseSyncronizer($warehouse);
        }

        return $warehouseSyncronizer;
    }

    public function makeInjections($instance)
    {
    }
}

