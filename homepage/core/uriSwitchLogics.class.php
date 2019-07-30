<?php

class uriSwitchLogics implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $languageCode = '';
    protected $application = '';
    /**
     * @var controller
     */
    protected $controller;
    /**
     * @var LanguagesManager
     */
    protected $languagesManager;
    /**
     * @var structureManager
     */
    protected $structureManager;
    /**
     * @var linksManager
     */
    protected $linksManager;

    public function __construct()
    {
        $this->controller = $this->getService('controller');
        $this->languagesManager = $this->getService('LanguagesManager');
        $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
        $this->structureManager = $this->getService('structureManager', ['rootMarker' => $marker], true);
        $this->linksManager = $this->getService('linksManager');
    }

    public function getMobileUrlBase()
    {
        return '//' . $this->controller->domainURL . 'mobile/';
    }

    public function findForeignRelativeUrl($elementId)
    {
        $url = '';
        if ($this->languageCode) {
            $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
            $this->languagesManager->setCurrentLanguageCode($this->languageCode, $marker);
            $targetLanguageId = $this->languagesManager->getCurrentLanguageId();

            $baseUrl = $this->controller->baseURL;
            if ($this->application && $this->application != 'public') {
                $baseUrl .= $this->application . '/';
            }
            $this->structureManager->setRootUrl($baseUrl);
            $this->structureManager->setRequestedPath([$this->languageCode]);

            if ($this->structureManager->checkElementInParent($elementId, $targetLanguageId)) {
                $element = $this->structureManager->getElementById($elementId, $targetLanguageId);
            } else {
                $element = $this->findForeignConnectedElement($elementId, $targetLanguageId);
            }

            if ($element) {
                $url = $element->getUrl();
            }

            if (!$url) {
                $url = $baseUrl . $this->languageCode . '/';
            }
        }
        return $url;
    }

    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function setApplication($application)
    {
        $this->application = $application;
    }

    protected function findForeignConnectedElement($id, $languageId)
    {
        $relative = false;
        if ($connectedIds = $this->linksManager->getConnectedIdList($id, 'foreignRelative', 'parent')) {
            foreach ($connectedIds as &$connectedId) {
                if ($element = $this->structureManager->getElementById($connectedId, $languageId)) {
                    $relative = $element;
                    break;
                }
            }
        }
        return $relative;
    }
}