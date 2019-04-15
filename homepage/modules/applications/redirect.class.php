<?php

class redirectApplication extends controllerApplication
{
    protected $applicationName = 'redirect';
    public $rendererName = 'smarty';

    public function initialize()
    {
        $this->startSession('public', $this->getService('ConfigManager')->get('main.publicSessionLifeTime'));
        $this->createRenderer();
    }

    public function execute($controller)
    {
        if ($type = $controller->getParameter('type')) {
            $redirectionManager = $this->getService('redirectionManager');
            if ($type == 'language') {
                if (!($application = $controller->getParameter('application'))){
                    $application = 'public';
                }
                $sourceElementId = $controller->getParameter('element');
                $newLanguageCode = $controller->getParameter('code');

                $redirectionManager->switchLanguage($newLanguageCode, $sourceElementId, $application);
            } elseif ($type == 'element') {
                $redirectionManager->redirectToElement($controller->getParameter('id'));
            }
        }
        $this->renderer->fileNotFound();
    }

    public function getUrlName()
    {
        return '';
    }
}
