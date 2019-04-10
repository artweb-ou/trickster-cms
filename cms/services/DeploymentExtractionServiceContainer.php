<?php

class DeploymentExtractionServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new  DeploymentExtraction();
    }

    public function makeInjections($instance)
    {
    }
}
