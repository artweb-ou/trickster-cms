<?php

class VerifyMailAdapterServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new VerifyMailAdapter();
    }

    public function makeInjections($instance)
    {
        $verifyMail = $instance;
        if ($configManager = $this->registry->getService('ConfigManager')) {
            $verifyMail->setConfigManager($configManager);
        }
        return $verifyMail;
    }
}