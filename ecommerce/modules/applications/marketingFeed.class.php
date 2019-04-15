<?php

class marketingFeedApplication extends controllerApplication
{
    protected $applicationName = '';
    public $rendererName = 'smarty';
    public $themeCode = '';
    public $requestParameters = array('language');
    public $config;

    public function initialize()
    {
        $this->createRenderer();
    }

    public function execute($controller)
    {
        $configManager = $this->getService('configManager');
        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->baseURL,
            'rootMarker'  => $configManager->get('main.rootMarkerPublic')
        ], true);
        $languagesManager = $this->getService('languagesManager');
        if ($language = $controller->getParameter('language')) {
            $languagesManager->setCurrentLanguageCode($language);
        }
        $currencySelector = $this->getService('CurrencySelector');
        $currencySymbol = strtoupper($currencySelector->getSelectedCurrencyCode());
        $languageId = $languagesManager->getCurrentLanguageId();
        $structureManager->buildRequestedPath($controller->requestedPath);
        $structureManager->getElementById($languageId);
        $requestedTheme = 'marketingFeed';
        $type =  'product';
        $productElements = $structureManager->getElementsByType($type, $languageId);
        $designThemesManager = $this->getService('designThemesManager');
        $currentTheme = $designThemesManager->getTheme($requestedTheme);
        $this->renderer->setContentType('application/xml');
        $this->renderer->setCacheControl('no-cache');
        $this->renderer->setContentDisposition('inline');
        $this->renderer->assign('products', $productElements);
        $this->renderer->assign('currencySymbol', $currencySymbol);
        $this->renderer->assign('controller', $controller);
        $this->renderer->template = $currentTheme->template('marketing_feed.index.tpl');

        $this->renderer->display();
    }

    public function getUrlName()
    {
        return '';
    }

    public function getThemeCode()
    {
        return $this->themeCode;
    }
}

?>