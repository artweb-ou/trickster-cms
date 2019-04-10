<?php

class productsImportManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new productsImportManager();
    }

    public function makeInjections($instance)
    {
    }
}

