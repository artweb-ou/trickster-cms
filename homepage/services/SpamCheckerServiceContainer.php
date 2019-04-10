<?php

class SpamCheckerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new SpamChecker();
    }

    public function makeInjections($instance)
    {
        $spamChecker = $instance;
        if ($configManager = $this->registry->getService('ConfigManager')) {
            $spamChecker->setConfigManager($configManager);
        }

        return $spamChecker;
    }
}