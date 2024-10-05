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
        $structureManager = $this->getService(
            'structureManager',
            [
                'rootUrl' => $controller->rootURL,
                'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
            ],
            true
        );
        if ($type = $controller->getParameter('type')) {
            $redirectionManager = $this->getService('RedirectionManager');
            if ($type === 'language') {
                if (!($application = $controller->getParameter('application'))) {
                    $application = 'public';
                }
                $sourceElementId = $controller->getParameter('element');
                $languageCode = $controller->getParameter('code');

                $redirectionManager->switchLanguage($languageCode, $sourceElementId, $application);
            } elseif ($type === 'element') {
                $sourceElementId = $controller->getParameter('id');
                $languageCode = $controller->getParameter('code');
                $redirectionManager->redirectToElement($sourceElementId, $languageCode);
            }
        }
        $this->renderer->fileNotFound();
    }

    public function getUrlName()
    {
        return '';
    }
}
