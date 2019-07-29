<?php

class ajaxProductListApplication extends controllerApplication
{
    public $rendererName = 'json';
    protected $applicationName = 'ajaxProductList';
    protected $mode = 'public';

    public function initialize()
    {
        $controller = controller::getInstance();
        if ($controller->getParameter('mode')) {
            $mode = $controller->getParameter('mode');
            if ($mode == 'admin') {
                $this->mode = 'admin';
            } else {
                $this->mode = 'public';
            }
        }
        $configManager = $controller->getConfigManager();
        if ($this->mode == 'admin') {
            $this->startSession($this->mode, $configManager->get('main.adminSessionLifeTime'));
        } else {
            $this->startSession($this->mode, $configManager->get('main.publicSessionLifeTime'));
        }

        $this->createRenderer();
    }

    public function execute($controller)
    {
        /**
         * @var Cache $cache
         */
        //        $cache = $this->getService('Cache');
        //        $cache->enable();

        $response = new ajaxResponse();
        $languagesManager = $this->getService('languagesManager');

        $response->setPreset('detailed');
        /**
         * @var structureManager $structureManager
         */

        $structureManager = $this->getService('structureManager', [
            'rootUrl'       => $controller->rootURL,
            'rootMarker'    => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
            'configActions' => false,
        ], true);
        $structureManager->setRequestedPath([$languagesManager->getCurrentLanguageCode()]);

        $productsValue = [];
        if ($elementId = $controller->getParameter('elementId')) {
            if ($element = $structureManager->getElementById($elementId)) {
//                if ($controller->getParameter('limit')) {
//                    
//                }
                $products = $element->getProductsList();
            }
        }

        //        $products = $structureManager->getProductsByCategory($category);
        //
                            $response->setResponseData("product", $products);
                            $response->setResponseData("listInfo", $products);

        $status = 'success';
        $this->renderer->assign('responseStatus', $status);
        $this->renderer->assign('responseData', $response->responseData);

        $this->renderer->setCacheControl('no-cache');
        $this->renderer->display();
    }

    public function getUrlName()
    {
        if ($this->mode == 'admin') {
            return 'admin';
        } else {
            return '';
        }
    }
}

