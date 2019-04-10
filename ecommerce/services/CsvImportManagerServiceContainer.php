<?php

class CsvImportManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new CsvImportManager();
    }

    public function makeInjections($instance)
    {
    }
}