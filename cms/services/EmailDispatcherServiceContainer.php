<?php

class EmailDispatcherServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new EmailDispatcher();
    }

    public function makeInjections($instance)
    {
        $emailDispatcher = $instance;
        $designThemesManager = $this->registry->getService('DesignThemesManager');
        $emailDispatcher->setDesignThemesManager($designThemesManager);

        if (($timeLimit = $this->getOption('timeLimit')) === false) {
            if (defined('EMAIL_DISPATCHMENT_TIME_LIMIT')) {
                // deprecated since 2016.03
                $timeLimit = EMAIL_DISPATCHMENT_TIME_LIMIT;
            } else {
                $configManager = $this->registry->getService('ConfigManager');
                $timeLimit = $configManager->get('emails.timeLimit');
            }
        }

        $emailDispatcher->setTimeLimit($timeLimit);
        return $emailDispatcher;
    }
}