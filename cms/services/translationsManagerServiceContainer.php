<?php

class translationsManagerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new translationsManager();
    }

    /**
     * @param translationsManager $instance
     */
    public function makeInjections($instance)
    {
        $configManager = $this->registry->getService('ConfigManager');
        $instance->enableLogging($configManager->get('main.logMissingTranslations'));
    }
}