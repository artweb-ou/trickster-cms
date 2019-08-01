<?php

class ajaxProductListApplication extends controllerApplication
{
    public $rendererName = 'json';
    protected $applicationName = 'ajaxProductList';
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
        /**
         * @var structureManager $structureManager
         */

        $structureManager = $this->getService('structureManager', [
            'rootUrl'       => $controller->rootURL,
            'rootMarker'    => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
            'configActions' => false,
        ], true);
        $structureManager->setRequestedPath([$languagesManager->getCurrentLanguageCode()]);
        $status = 'fail';

        $categoryFilters = [];
        if ($elementId = $controller->getParameter('elementId')) {
            if ($element = $structureManager->getElementById($elementId)) {
                if ($element instanceof ProductsListElement) {
                    $status = 'success';

                    $products = $element->getProductsList();
                    $filters = $element->getFilters();

                    foreach ($filters as $nr => $filter) {
                        if ($filter instanceof ProductFilter) {
                            $categoryFilters['filters'][] = [
                                'type'      => $filter->getType(),
                                'title'     => $filter->getTitle(),
                                'options'    => $filter->getOptionsInfo(),
                            ];
                        }
                    }
                }
            }
        }

        $response->setResponseData("product", $products);
        $response->setResponseData("listInfo", $categoryFilters);

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

