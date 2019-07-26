<?php

class ajaxApplication extends controllerApplication
{
    protected $applicationName = 'ajax';
    public $rendererName = 'json';

    public function initialize()
    {
        $this->startSession('public', $this->getService('ConfigManager')->get('main.publicSessionLifeTime'));
        $this->createRenderer();
    }

    public function execute($controller)
    {
        /**
         * @var Cache $cache
         */
        $cache = $this->getService('Cache');
        $cache->enable(true, false, true);

        $currentElement = false;
        $this->renderer->assign('responseData', []);

        if ($controller->getParameter('id')) {
            //todo: replace with $controller->rootURL and test.
            $structureManager = $this->getService('structureManager', [
                'rootUrl' => $controller->baseURL,
                'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
            ], true);

            $this->processRequestParameters();

            $languagesManager = $this->getService('LanguagesManager');
            $elementId = $controller->getParameter('id');

            if (is_numeric($elementId)) {
                $currentElement = $structureManager->getElementById($elementId, $languagesManager->getCurrentLanguageId());
            } else {
                $currentElement = $structureManager->getCurrentElement();
            }
        }
        if ($currentElement) {
            if ($this->renderer->getAttribute('responseStatus') === false) {
                $this->renderer->assign('responseStatus', 'success');
            }
            $this->renderer->display();
        } else {
            $this->renderer->assign('responseStatus', 'fail');
            $this->renderer->fileNotFound();
        }
    }

    public function processRequestParameters()
    {
        $structureManager = $this->getService('structureManager');
        $controller = controller::getInstance();

        if ($controller->getParameter('type')) {
            if ($controller->getParameter('action')) {
                $requestedPath = implode('/', $controller->requestedPath) . '/';
                $structureManager->newElementParameters[$requestedPath]['action'] = $controller->getParameter('action');
                $structureManager->newElementParameters[$requestedPath]['type'] = $controller->getParameter('type');

                if ($controller->getParameter('linkType')) {
                    $structureManager->setNewElementLinkType($controller->getParameter('linkType'));
                }
            }
        } elseif ($controller->getParameter('action') && $controller->getParameter('id')) {
            $structureManager->customActions[$controller->getParameter('id')] = $controller->getParameter('action');
        }
    }

    public function getUrlName()
    {
        return '';
    }
}