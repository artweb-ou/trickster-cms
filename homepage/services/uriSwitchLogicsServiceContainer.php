<?php

class uriSwitchLogicsServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new uriSwitchLogics();
    }

    public function makeInjections($instance)
    {
        $this->injectService($instance, 'controller');
        $this->injectService($instance, 'LanguagesManager');
        $this->injectService($instance, 'structureManager');
        $this->injectService($instance, 'linksManager');
    }
}
