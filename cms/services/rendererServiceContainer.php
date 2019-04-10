<?php

class rendererServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        if ($name = $this->getOption('name')) {
            $renderer = renderer::createInstance($name);
        } else {
            $renderer = renderer::getInstance();
        }
        return $renderer;
    }

    public function makeInjections($instance)
    {
    }
}
