<?php

class javascriptApplication extends controllerApplication
{
    protected $applicationName = 'javascript';
    public $rendererName = 'javascriptUniter';

    public function initialize()
    {
        $this->createRenderer();
    }

    public function execute($controller)
    {
        if ($fileName = $controller->getParameter('file')) {
            $this->renderer->assign('fileName', $fileName);
        }

        if ($set = $controller->getParameter('set')) {
            $currentThemeCode = $set;
        } else {
            $currentThemeCode = 'default';
        }

        $designThemesManager = $this->getService(DesignThemesManager::class);
        $designThemesManager->setCurrentThemeCode($currentThemeCode);
        $resourcesUniterHelper = $this->getService(ResourcesUniterHelper::class);
        $cacheFileName = $resourcesUniterHelper->getCacheCode();
        $javascriptResources = $resourcesUniterHelper->getJavascriptResources($controller);

        $this->renderer->assign('cacheFileName', $cacheFileName);
        $this->renderer->assign('resources', $javascriptResources);
        $this->renderer->display();
    }
}