<?php

class jsonElementDataApplication extends controllerApplication
{
    use DbLoggableApplication;

    public $rendererName = 'json';
    protected $applicationName = 'jsonElementData';
    protected $mode = 'public';

    public function initialize()
    {
        $controller = controller::getInstance();
        $configManager = $controller->getConfigManager();
        $this->startSession($this->mode, $configManager->get('main.publicSessionLifeTime'));

        $this->createRenderer();
    }

    public function execute($controller)
    {
        $this->startDbLogging();
        /**
         * @var Cache $cache
         */
        $cache = $this->getService('Cache');
        $cache->enable();

        $response = new ajaxResponse();
        $languagesManager = $this->getService('languagesManager');

        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->rootURL,
            'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
            'configActions' => false,
        ], true);
        $structureManager->setRequestedPath([$languagesManager->getCurrentLanguageCode()]);
        $status = 'fail';
        $preset = 'details';
        if ($baseElementId = $controller->getParameter('baseElementId')) {
            if ($baseElement = $structureManager->getElementById($baseElementId)) {
                if ($baseElement instanceof JsonDataProvider) {
                    $status = 'success';
                    $response->setResponseData("zxProdsList", $baseElement->getElementData($preset));
                }
            }
        }
        $this->renderer->assign('responseStatus', $status);
        $this->renderer->assign('responseData', $response->responseData);

        $this->renderer->setCacheControl('no-cache');
        $this->renderer->display();
        $this->saveDbLog();
    }

    public function getUrlName()
    {
        return '';
    }
}

