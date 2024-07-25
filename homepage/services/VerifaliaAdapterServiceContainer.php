<?php

class VerifaliaAdapterServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new VerifaliaAdapter();
    }

    public function makeInjections($instance)
    {
        $verifalia = $instance;
        if ($configManager = $this->registry->getService('ConfigManager')) {
            $verifalia->setConfigManager($configManager);
        }

        return $verifalia;
    }
}