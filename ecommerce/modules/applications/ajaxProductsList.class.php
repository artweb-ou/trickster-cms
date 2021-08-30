<?php

class ajaxProductsListApplication extends controllerApplication
{
    use DbLoggableApplication;

    public $rendererName = 'json';
    protected $applicationName = 'ajaxProductsList';
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
        $languagesManager = $this->getService('LanguagesManager');

        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->rootURL,
            'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
            'configActions' => false,
        ], true);
        $structureManager->setRequestedPath([$languagesManager->getCurrentLanguageCode()]);
        $status = 'fail';

        if ($listElementId = $controller->getParameter('listElementId')) {
            if ($productsListElement = $structureManager->getElementById($listElementId)) {
                if ($productsListElement instanceof ProductsListElement) {
                    $status = 'success';
                    $response->setResponseData("productsList", $productsListElement->getElementData('list'));
                    if ($elements = $structureManager->getElementsByType('productSearch', $languagesManager->getCurrentLanguageId())){
                        foreach ($elements as $element){
                            $element->setProductsListElement($productsListElement);
                        }
                        $response->setPreset('list');
                        $response->setResponseData("productSearch", $elements);
                    }
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

