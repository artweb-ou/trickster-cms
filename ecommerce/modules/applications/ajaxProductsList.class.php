<?php

class ajaxProductsListApplication extends controllerApplication
{
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
        $response = new ajaxResponse();
        $languagesManager = $this->getService('languagesManager');

        $response->setPreset('detailed');

        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->rootURL,
            'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
            'configActions' => false,
        ], true);
        $structureManager->setRequestedPath([$languagesManager->getCurrentLanguageCode()]);
        $status = 'fail';

        $categoryFilters = [];
        $products = [];
        if ($listElementId = $controller->getParameter('listElementId')) {
            if ($element = $structureManager->getElementById($listElementId)) {
                if ($element instanceof ProductsListElement) {
                    $status = 'success';

                    if ($elementId = $controller->getParameter('elementId')) {
                        $products = $element->getSingleProduct($elementId);
                    } else {
                        $products = $element->getProductsList();
                    }
                    $filters = $element->getFilters();

                    foreach ($filters as $filter) {
                        $categoryFilters['filters'][] = [
                            'type' => $filter->getType(),
                            'id' => $filter->getId(),
                            'title' => $filter->getTitle(),
                            'options' => $filter->getOptionsInfo(),
                        ];
                    }
                }
            }
        }

        $response->setResponseData("product", $products);
        $response->setResponseData("filters", $categoryFilters);
        $response->setResponseData("pagerDefaultLimit", $element->getDefaultLimit());

        $this->renderer->assign('responseStatus', $status);
        $this->renderer->assign('responseData', $response->responseData);

        $this->renderer->setCacheControl('no-cache');
        $this->renderer->display();
    }

    public function getUrlName()
    {
        return '';
    }
}

